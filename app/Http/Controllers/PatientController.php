<?php

namespace App\Http\Controllers;

use App\Models\AhcsPatient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * List patients from the external AHCS database (read-only).
     */
    public function index(Request $request)
    {
        $query = AhcsPatient::query();

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mrn', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $patients = $query->latest()->paginate(20);

        return view('patients.index', compact('patients'));
    }

    /**
     * Show a single AHCS patient profile with their submissions and assignments.
     */
    public function show(AhcsPatient $patient)
    {
        $patient->load(['formSubmissions.form', 'messages']);

        return view('patients.show', compact('patient'));
    }
}
