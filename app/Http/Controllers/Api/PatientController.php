<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AhcsPatient;
use App\Models\AhcsCase;
use App\Models\AhcsIntake;
use App\Models\AhcsMedAuth;
use App\Models\AhcsWorkComp;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PatientController extends Controller
{
    public function getPatientDetails(Request $request): JsonResponse
    {
        try {
            $patient_id = $request->query('patient_id');
            $case_id = $request->query('case_id');

            if (!$patient_id) {
                throw new \Exception("Patient ID is required", 400);
            }

            if (!$case_id) {
                throw new \Exception("Case ID is required", 400);
            }

            // ✅ Use findOrFail (auto throw)
            $patient = AhcsPatient::findOrFail($patient_id);

            // ✅ Ensure case belongs to patient
            $case = AhcsCase::where('patient_id', $patient_id)
                ->where('id', $case_id)
                ->first();

            if (!$case) {
                throw new \Exception("Case not found for the given patient", 404);
            }

            $med_auth = AhcsMedAuth::where('case_id', $case_id)->first();
            if (!$med_auth) {
                throw new \Exception("MedAuth not found for the given case", 404);
            }

            $intake = AhcsIntake::where('patient_id', $patient_id)->first();
            if (!$intake) {
                throw new \Exception("Intake not found for the given patient", 404);
            }

            $workcamp = AhcsWorkComp::where('patient_id', $patient_id)->first();
            if (!$workcamp) {
                throw new \Exception("WorkComp not found for the given patient", 404);
            }

            return response()->json([
                'status' => 'success',
                'patient_details' => $patient->toArray(),
                'case_details' => $case->toArray(),
                'med_auth_details' => $med_auth->toArray(),
                'intake_details' => $intake->toArray(),
                'workcamp_details' => $workcamp->toArray(),
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Patient not found'
            ], 404);

        } catch (\Throwable $e) {
            Log::error("Error fetching patient details: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }
}
