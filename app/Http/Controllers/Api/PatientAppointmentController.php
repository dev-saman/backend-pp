<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AhcsPatient;
use App\Models\AhcsCase;
use App\Models\AhcsMedAuth;
use App\Models\AhcsAttendance;
use App\Models\MedhiwaSpecialityLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PatientAppointmentController extends Controller
{
    public function getPatientAppointments(Request $request): JsonResponse
    {
        try {
            $patientId = $request->query('patient_id');
            $caseId = $request->query('case_id');

            if (!$patientId || !$caseId) {
                throw new \Exception("Patient ID and Case ID are required", 400);
            }

            // ✅ Check patient exists
            AhcsPatient::findOrFail($patientId);

            // ✅ Check case belongs to patient
            $caseExists = AhcsCase::where('id', $caseId)
                ->where('patient_id', $patientId)
                ->exists();

            if (!$caseExists) {
                throw new \Exception("Case not found for the given patient", 404);
            }

            // ✅ Get MedAuth IDs directly (no need to store collection if empty check not critical)
            $medAuthIds = AhcsMedAuth::where('case_id', $caseId)->pluck('id');

            if ($medAuthIds->isEmpty()) {
                throw new \Exception("No MedAuth records found for the given case", 404);
            }

            // ✅ Fetch appointments
            $appointments = AhcsAttendance::whereIn('ma_id', $medAuthIds)
                ->whereNotIn('attend_status', ['DL', 'Block','RS'])
                ->get();

            // ✅ Split into upcoming & past
            $today = now()->startOfDay();
            
            $upcoming = [];
            $past = [];

            foreach ($appointments as $appointment) {
                if ($appointment->attend_date >= $today) {
                    $upcoming[] = $appointment;
                } else {
                    $past[] = $appointment;
                }
            }

            return response()->json([
                'status' => 'success',
                'upcoming_count' => count($upcoming),
                'past_count' => count($past),
                'upcoming_appointments' => $upcoming,
                'past_appointments' => $past
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Patient not found'
            ], 404);

        } catch (\Throwable $e) {
            Log::error("Error fetching patient appointments: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => $e->getCode() == 400 || $e->getCode() == 404 
                    ? $e->getMessage() 
                    : 'Something went wrong'
            ], $e->getCode() ?: 500);
        }
    }

    public function getAppointmentDepartments(){
        try {

            $departments = MedhiwaSpecialityLocation::where('status', 1)
                            ->whereNull('deleted_at')
                            ->pluck('city');
            
            return response()->json([
                'status' => 'success',
                'departments' => $departments
            ], 200);
        } catch (\Throwable $e) {
            Log::error("Error fetching appointment departments: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong'
            ], 500);
        }
    }


    public function GetSpecialityWithPhysician($city)
    {

        $dbPhysician = DB::connection('physician');

        /* ------------------------------------
         | 1. Get mapped specialities for this location
         |-------------------------------------*/
        $rows = SpecialityLocation::getLocationsWithSpecialities()
            ->where('city', $city);

        if ($rows->isEmpty()) {
            return response()->json([
                'status' => true,
                'count' => 0,
                'data' => []
            ]);
        }

        /* ------------------------------------
         | 2. Build unique speciality list
         |-------------------------------------*/
        $specialities = $rows
            ->unique('speciality_id')
            ->map(function ($row) {
                return (object) [
                    'id' => $row->speciality_id,
                    'name' => $row->name,
                    'short_name' => $row->short_name,
                    'allow_multiple' => $row->allow_multiple,
                    'multiple_allowed_slots' => $row->multiple_allowed_slots,
                    'multiple_slot_duration' => $row->multiple_slot_duration,
                    'parent_id' => $row->parent_id ?? null,
                ];
            })
            ->keyBy('short_name');

        /* ------------------------------------
         | 3. Get ACTIVE Physicians for current location
         | - Filter by is_active = 1 (only active providers)
         | - Filter by physician_type = 'internal'
         | - Get working hours for current location
         |-------------------------------------*/
        $physicians = $dbPhysician
            ->table('physicians as p')
            ->join('physician_addresses as pa', function ($join) {
                $join->on('pa.physician_id', '=', 'p.id')
                    ->whereNull('pa.deleted_at'); // Soft delete filter
            })
            ->where('pa.physician_city', $city)
            ->where('p.physician_type', 'internal')
            ->where('p.is_deleted', 1)
            ->where('p.is_active', 1) // Only active providers
            ->select(
                'p.id as physician_id',
                'p.physician_name',
                'p.speciality_short',
                'p.is_aids_tech',
                'p.schedule_type',
                'pa.amd_physician_code as provider_code',
                'pa.physician_sun',
                'pa.physician_mon',
                'pa.physician_tue',
                'pa.physician_wed',
                'pa.physician_thu',
                'pa.physician_fri',
                'pa.physician_sat',
                'pa.physician_sun_open',
                'pa.physician_mon_open',
                'pa.physician_tue_open',
                'pa.physician_wed_open',
                'pa.physician_thu_open',
                'pa.physician_fri_open',
                'pa.physician_sat_open',
                'pa.physician_sun_close',
                'pa.physician_mon_close',
                'pa.physician_tue_close',
                'pa.physician_wed_close',
                'pa.physician_thu_close',
                'pa.physician_fri_close',
                'pa.physician_sat_close',
                'pa.lunch_time_start',
                'pa.lunch_time_end',
                'pa.lunch_time_enabled',
                'pa.is_telemed',
                // Day-wise telemed flags
                'pa.telemed_sun',
                'pa.telemed_mon',
                'pa.telemed_tue',
                'pa.telemed_wed',
                'pa.telemed_thu',
                'pa.telemed_fri',
                'pa.telemed_sat'
            )
            ->get();

        /* ------------------------------------
         | 4. Get OTHER location addresses for multi-location providers
         | Fetch all addresses for providers who work at the current location
         | but exclude the current location itself
         |-------------------------------------*/
        $physicianIds = $physicians->pluck('physician_id')->unique()->toArray();

        $otherLocations = collect();
        if (!empty($physicianIds)) {
            $otherLocations = $dbPhysician
                ->table('physician_addresses')
                ->whereIn('physician_id', $physicianIds)
                ->where('physician_city', '!=', $city) // Exclude current location
                ->whereNull('deleted_at') // Soft delete filter
                ->select(
                    'physician_id',
                    'physician_city',
                    'amd_physician_code as provider_code',
                    'physician_sun',
                    'physician_mon',
                    'physician_tue',
                    'physician_wed',
                    'physician_thu',
                    'physician_fri',
                    'physician_sat',
                    'physician_sun_open',
                    'physician_mon_open',
                    'physician_tue_open',
                    'physician_wed_open',
                    'physician_thu_open',
                    'physician_fri_open',
                    'physician_sat_open',
                    'physician_sun_close',
                    'physician_mon_close',
                    'physician_tue_close',
                    'physician_wed_close',
                    'physician_thu_close',
                    'physician_fri_close',
                    'physician_sat_close',
                    'lunch_time_start',
                    'lunch_time_end',
                    'lunch_time_enabled',
                    'is_telemed',
                    // Day-wise telemed flags
                    'telemed_sun',
                    'telemed_mon',
                    'telemed_tue',
                    'telemed_wed',
                    'telemed_thu',
                    'telemed_fri',
                    'telemed_sat'
                )
                ->get()
                ->groupBy('physician_id');
        }

        /* ------------------------------------
         | 5. Attach other_locations to each physician
         | This allows the frontend to show "Working in [Location]"
         | when the provider is scheduled at another location
         |-------------------------------------*/
        $physiciansWithOtherLocations = $physicians->map(function ($physician) use ($otherLocations, $city) { // Added by Devin – 17-Mar-2026: pass $city for monthly availability query
            $physicianOtherLocs = $otherLocations[$physician->physician_id] ?? collect();

            // Format other locations with their working hours
            $physician->other_locations = $physicianOtherLocs->map(function ($loc) use ($physician) { // Added by Devin – 17-Mar-2026: pass $physician to closure for schedule_type check
                $locationData = [
                    'city' => $loc->physician_city,
                    'provider_code' => $loc->provider_code,
                    'physician_sun' => $loc->physician_sun,
                    'physician_mon' => $loc->physician_mon,
                    'physician_tue' => $loc->physician_tue,
                    'physician_wed' => $loc->physician_wed,
                    'physician_thu' => $loc->physician_thu,
                    'physician_fri' => $loc->physician_fri,
                    'physician_sat' => $loc->physician_sat,
                    'physician_sun_open' => $loc->physician_sun_open,
                    'physician_mon_open' => $loc->physician_mon_open,
                    'physician_tue_open' => $loc->physician_tue_open,
                    'physician_wed_open' => $loc->physician_wed_open,
                    'physician_thu_open' => $loc->physician_thu_open,
                    'physician_fri_open' => $loc->physician_fri_open,
                    'physician_sat_open' => $loc->physician_sat_open,
                    'physician_sun_close' => $loc->physician_sun_close,
                    'physician_mon_close' => $loc->physician_mon_close,
                    'physician_tue_close' => $loc->physician_tue_close,
                    'physician_wed_close' => $loc->physician_wed_close,
                    'physician_thu_close' => $loc->physician_thu_close,
                    'physician_fri_close' => $loc->physician_fri_close,
                    'physician_sat_close' => $loc->physician_sat_close,
                    'lunch_time_start' => $loc->lunch_time_start,
                    'lunch_time_end' => $loc->lunch_time_end,
                    'lunch_time_enabled' => (int) ($loc->lunch_time_enabled ?? 1),
                    'is_telemed' => (bool) $loc->is_telemed,
                    // Day-wise telemed flags
                    'telemed_sun' => (int) ($loc->telemed_sun ?? 1),
                    'telemed_mon' => (int) ($loc->telemed_mon ?? 1),
                    'telemed_tue' => (int) ($loc->telemed_tue ?? 1),
                    'telemed_wed' => (int) ($loc->telemed_wed ?? 1),
                    'telemed_thu' => (int) ($loc->telemed_thu ?? 1),
                    'telemed_fri' => (int) ($loc->telemed_fri ?? 1),
                    'telemed_sat' => (int) ($loc->telemed_sat ?? 1),
                ];

                return $locationData;
            })->values()->toArray();

            // Added by Devin – 17-Mar-2026: Include monthly availability for the CURRENT location
            // Only fetch if the provider uses monthly schedule
            if (($physician->schedule_type ?? 'weekly') === 'monthly') {
                $physician->monthly_availability = ProviderMonthlyAvailability::where('provider_id', $physician->physician_id)
                    ->where('provider_city', $city)
                    ->select('available_date as date', 'open_time', 'close_time')
                    ->orderBy('available_date')
                    ->get()
                    ->toArray();
            } else {
                $physician->monthly_availability = [];
            }

            // Include custom lunch times (today and future only)
            $physician->custom_lunch_times = ProviderCustomLunchTime::where('physician_id', $physician->physician_id)
                ->where('custom_date', '>=', now()->toDateString())
                ->select('id', 'custom_date as date', 'lunch_start', 'lunch_end', 'lunch_enabled')
                ->orderBy('custom_date')
                ->get()
                ->toArray();

            return $physician;
        });

        /* ------------------------------------
         | 5b. Build physician-to-speciality mapping using physician_specialties pivot table
         | This ensures multi-speciality providers appear under ALL their specialities
         |-------------------------------------*/
        $physicianSpecialties = collect();
        if (!empty($physicianIds)) {
            $physicianSpecialties = $dbPhysician
                ->table('physician_specialties')
                ->whereIn('physician_id', $physicianIds)
                ->select('physician_id', 'specialty')
                ->get();
        }

        // Build a map: speciality_short_name => [physician_ids]
        // First, create a map of speciality full name to short name
        $specNameToShort = $specialities->mapWithKeys(function ($spec) {
            return [$spec->name => $spec->short_name];
        })->toArray();

        // Group physicians by speciality short name using the pivot table
        // For providers WITHOUT pivot entries, fall back to the speciality_short column
        $physiciansBySpecShort = collect();
        $physiciansWithPivot = collect(); // Track which physicians have pivot entries

        foreach ($physicianSpecialties as $ps) {
            $shortName = $specNameToShort[$ps->specialty] ?? null;
            if ($shortName) {
                if (!$physiciansBySpecShort->has($shortName)) {
                    $physiciansBySpecShort[$shortName] = collect();
                }
                $physician = $physiciansWithOtherLocations->firstWhere('physician_id', $ps->physician_id);
                if ($physician) {
                    $physiciansBySpecShort[$shortName]->push($physician);
                    $physiciansWithPivot->push($ps->physician_id);
                }
            }
        }

        // Fall back: physicians without pivot entries use their speciality_short column
        $physiciansWithPivotIds = $physiciansWithPivot->unique()->toArray();
        foreach ($physiciansWithOtherLocations as $physician) {
            if (!in_array($physician->physician_id, $physiciansWithPivotIds)) {
                $shortName = $physician->speciality_short;
                if ($shortName) {
                    if (!$physiciansBySpecShort->has($shortName)) {
                        $physiciansBySpecShort[$shortName] = collect();
                    }
                    $physiciansBySpecShort[$shortName]->push($physician);
                }
            }
        }

        /* ------------------------------------
         | 6. Fetch visit types with allow_per_slot for each speciality
         |-------------------------------------*/
        $specialityIds = $specialities->pluck('id')->toArray();
        $visitTypesBySpeciality = collect();
        if (!empty($specialityIds)) {
            $visitTypesBySpeciality = MedSpecialityVisitType::with(['orderType', 'duration'])
                ->whereIn('med_speciality_id', $specialityIds)
                ->whereNull('deleted_at')
                ->get()
                ->groupBy('med_speciality_id');
        }

        /* ------------------------------------
         | 7. Merge specialities with physicians and visit types
         |-------------------------------------*/
        $finalData = $specialities->map(function ($spec) use ($physiciansBySpecShort, $visitTypesBySpeciality) {
            $physicianList = $physiciansBySpecShort[$spec->short_name] ?? collect();

            $specVisitTypes = ($visitTypesBySpeciality[$spec->id] ?? collect())
                ->filter(fn($vt) => !is_null($vt->visittype_id))
                ->groupBy('visittype_id')
                ->map(function ($visitItems) {
                    $visit = $visitItems->first();
                    return [
                        'visittype_id' => $visit->visittype_id,
                        'visittype_name' => $visit->orderType->name ?? null,
                        'visittype_code' => $visit->orderType->code ?? null,
                        'allow_per_slot' => $visit->allow_per_slot,
                        'allow_multiple' => $visit->orderType->allow_multiple ?? 0,
                        'duration_slots' => $visitItems
                            ->filter(fn($item) => !is_null($item->duration_id))
                            ->unique('duration_id')
                            ->map(fn($item) => [
                                'duration_id' => $item->duration_id,
                                'duration_slot' => $item->duration->duration_slot ?? null,
                            ])
                            ->values(),
                    ];
                })
                ->values();

            return [
                'id' => $spec->id,
                'name' => $spec->name,
                'short_name' => $spec->short_name,
                'parent_id' => $spec->parent_id ?? null,
                'allow_multiple' => $spec->allow_multiple ?? 0,
                'multiple_allowed_slots' => $spec->multiple_allowed_slots ?? 0,
                'multiple_slot_duration' => $spec->multiple_slot_duration ?? 0,
                'physician_count' => $physicianList->count(),
                'physicians' => $physicianList->values(),
                'visit_types' => $specVisitTypes
            ];
        })->values();

        /* ------------------------------------
         | 7. Return JSON Response
         |-------------------------------------*/
        $responseData = [
            'status' => true,
            'count' => $finalData->count(),
            'data' => $finalData
        ];

        Cache::put($cacheKey, $responseData, 300);

        return response()->json($responseData);
    }
}
