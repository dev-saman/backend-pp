<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Funnel;
use App\Models\Patient;
use App\Models\PatientFunnelAssignment;
use App\Models\FunnelProgress;
use App\Models\FormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AssignmentController extends Controller
{
    /**
     * List all assignments (admin view)
     */
    public function index(Request $request)
    {
        $query = PatientFunnelAssignment::with(['patient', 'funnel', 'assignedBy'])
            ->latest();

        if ($request->patient_id) {
            $query->where('patient_id', $request->patient_id);
        }
        if ($request->funnel_id) {
            $query->where('funnel_id', $request->funnel_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $assignments = $query->paginate(20)->withQueryString();
        $funnels     = Funnel::whereIn('status', ['active', 'draft'])->orderBy('name')->get();
        $patients    = Patient::orderBy('first_name')->get();

        return view('assignments.index', compact('assignments', 'funnels', 'patients'));
    }

    /**
     * Assign a funnel to a patient (AJAX or form POST)
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'funnel_id'  => 'required|exists:funnels,id',
            'note'       => 'nullable|string|max:500',
            'expires_at' => 'nullable|date|after:now',
        ]);

        // Check if already assigned
        $existing = PatientFunnelAssignment::where('patient_id', $request->patient_id)
            ->where('funnel_id', $request->funnel_id)
            ->first();

        if ($existing) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'This funnel is already assigned to this patient.',
                    'assignment' => [
                        'id'       => $existing->id,
                        'token'    => $existing->token,
                        'fill_url' => $existing->fill_url,
                    ],
                ], 422);
            }
            return back()->with('error', 'This funnel is already assigned to this patient.');
        }

        $funnel  = Funnel::findOrFail($request->funnel_id);
        $formIds = $funnel->form_ids ?? [];

        $assignment = PatientFunnelAssignment::create([
            'patient_id'  => $request->patient_id,
            'funnel_id'   => $request->funnel_id,
            'assigned_by' => Auth::id(),
            'token'       => Str::random(40),
            'status'      => 'pending',
            'forms_total' => count($formIds),
            'note'        => $request->note,
            'expires_at'  => $request->expires_at,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status'     => 'success',
                'message'    => 'Funnel assigned successfully.',
                'assignment' => [
                    'id'              => $assignment->id,
                    'token'           => $assignment->token,
                    'fill_url'        => url('/fill/' . $assignment->token),
                    'status'          => $assignment->status,
                    'forms_total'     => $assignment->forms_total,
                    'progress_percent'=> 0,
                ],
            ]);
        }

        return back()->with('success', 'Funnel assigned to patient. Share this link: ' . url('/fill/' . $assignment->token));
    }

    /**
     * Show a single assignment with full progress detail
     */
    public function show(PatientFunnelAssignment $assignment)
    {
        $assignment->load(['patient', 'funnel', 'assignedBy', 'progress.form']);

        $formIds      = $assignment->funnel->form_ids ?? [];
        $forms        = Form::whereIn('id', $formIds)->get()->keyBy('id');
        $progressMap  = $assignment->progress->keyBy('form_id');

        $steps = collect($formIds)->map(function ($formId, $index) use ($forms, $progressMap) {
            $form     = $forms->get($formId);
            $progress = $progressMap->get($formId);
            return [
                'step'      => $index + 1,
                'form_id'   => $formId,
                'form_name' => $form?->name ?? 'Unknown Form',
                'status'    => $progress?->status ?? 'pending',
                'data'      => $progress?->data ?? [],
                'last_saved'=> $progress?->last_saved_at,
                'submitted' => $progress?->submitted_at,
            ];
        })->values();

        return view('assignments.show', compact('assignment', 'steps'));
    }

    /**
     * Delete an assignment
     */
    public function destroy(PatientFunnelAssignment $assignment)
    {
        $assignment->delete();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Assignment removed.']);
        }

        return back()->with('success', 'Assignment removed.');
    }

    /**
     * Resend the fill link (regenerate token)
     */
    public function resend(PatientFunnelAssignment $assignment)
    {
        $assignment->update(['token' => Str::random(40)]);

        return response()->json([
            'status'   => 'success',
            'message'  => 'New link generated.',
            'fill_url' => $assignment->fill_url,
        ]);
    }

    // ─── Progress Overview (Admin) ───────────────────────────────────────────────

    /**
     * Overview of all patient progress across all funnels
     */
    public function progressOverview(Request $request)
    {
        $query = PatientFunnelAssignment::with(['patient', 'funnel'])
            ->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $assignments = $query->paginate(25)->withQueryString();

        $stats = [
            'total'       => PatientFunnelAssignment::count(),
            'pending'     => PatientFunnelAssignment::where('status', 'pending')->count(),
            'in_progress' => PatientFunnelAssignment::where('status', 'in_progress')->count(),
            'completed'   => PatientFunnelAssignment::where('status', 'completed')->count(),
        ];

        return view('assignments.progress', compact('assignments', 'stats'));
    }

    /**
     * All funnels assigned to a specific patient
     */
    public function patientProgress(Patient $patient)
    {
        $assignments = PatientFunnelAssignment::with(['funnel', 'progress.form', 'assignedBy'])
            ->where('patient_id', $patient->id)
            ->latest()
            ->get();

        return view('assignments.patient-progress', compact('patient', 'assignments'));
    }

    /**
     * All patients assigned to a specific funnel
     */
    public function funnelProgress(Funnel $funnel)
    {
        $assignments = PatientFunnelAssignment::with(['patient', 'progress'])
            ->where('funnel_id', $funnel->id)
            ->latest()
            ->get();

        return view('assignments.funnel-progress', compact('funnel', 'assignments'));
    }

    // ─── Patient Fill Pages (Public) ────────────────────────────────────────────

    /**
     * Patient opens their unique fill link
     * Route: GET /fill/{token}
     */
    public function fillFunnel(string $token)
    {
        $assignment = PatientFunnelAssignment::with(['patient', 'funnel', 'progress'])
            ->where('token', $token)
            ->firstOrFail();

        // Check expiry
        if ($assignment->is_expired) {
            return view('assignments.expired', compact('assignment'));
        }

        $funnel  = $assignment->funnel;
        $formIds = $funnel->form_ids ?? [];
        $forms   = Form::whereIn('id', $formIds)->get()->keyBy('id');

        // Build ordered steps with existing progress data
        $progressMap = $assignment->progress->keyBy('form_id');

        $steps = collect($formIds)->map(function ($formId, $index) use ($forms, $progressMap) {
            $form     = $forms->get($formId);
            $progress = $progressMap->get($formId);
            return [
                'index'       => $index,
                'form_id'     => $formId,
                'form'        => $form,
                'status'      => $progress?->status ?? 'pending',
                'saved_data'  => $progress?->data ?? [],
                'last_saved'  => $progress?->last_saved_at?->diffForHumans(),
            ];
        })->filter(fn($s) => $s['form'] !== null)->values();

        // Find the current step (first non-completed)
        $currentStep = $steps->search(fn($s) => $s['status'] !== 'completed');
        if ($currentStep === false) $currentStep = $steps->count() - 1;

        // Update last accessed
        $assignment->update(['last_accessed_at' => now()]);
        if ($assignment->status === 'pending') {
            $assignment->update(['status' => 'in_progress']);
        }

        return view('assignments.fill', compact('assignment', 'funnel', 'steps', 'currentStep'));
    }

    /**
     * Auto-save a form's data (draft) — called every 30s or on field blur
     * Route: POST /fill/{token}/save
     */
    public function autoSave(Request $request, string $token)
    {
        $assignment = PatientFunnelAssignment::where('token', $token)->firstOrFail();

        $request->validate([
            'form_id' => 'required|integer|exists:forms,id',
            'data'    => 'required|array',
        ]);

        $formId     = $request->form_id;
        $stepIndex  = collect($assignment->funnel->form_ids ?? [])->search($formId) ?? 0;

        FunnelProgress::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'form_id'       => $formId,
            ],
            [
                'user_id'       => $assignment->assigned_by,
                'patient_id'    => $assignment->patient_id,
                'funnel_id'     => $assignment->funnel_id,
                'step_index'    => $stepIndex,
                'status'        => 'draft',
                'data'          => $request->data,
                'last_saved_at' => now(),
            ]
        );

        return response()->json([
            'status'     => 'success',
            'message'    => 'Progress saved.',
            'saved_at'   => now()->format('g:i A'),
        ]);
    }

    /**
     * Submit a single step/form in the funnel
     * Route: POST /fill/{token}/submit-step
     */
    public function submitStep(Request $request, string $token)
    {
        $assignment = PatientFunnelAssignment::with('funnel')->where('token', $token)->firstOrFail();

        $request->validate([
            'form_id' => 'required|integer|exists:forms,id',
            'data'    => 'required|array',
        ]);

        $formId    = $request->form_id;
        $stepIndex = collect($assignment->funnel->form_ids ?? [])->search($formId) ?? 0;

        // Mark this step as completed
        FunnelProgress::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'form_id'       => $formId,
            ],
            [
                'user_id'      => $assignment->assigned_by,
                'patient_id'   => $assignment->patient_id,
                'funnel_id'    => $assignment->funnel_id,
                'step_index'   => $stepIndex,
                'status'       => 'completed',
                'data'         => $request->data,
                'last_saved_at'=> now(),
                'submitted_at' => now(),
            ]
        );

        // Also save to form_submissions for reporting
        FormSubmission::create([
            'user_id'    => $assignment->assigned_by,
            'form_id'    => $formId,
            'patient_id' => $assignment->patient_id,
            'funnel_id'  => $assignment->funnel_id,
            'data'       => $request->data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Recalculate overall progress
        $assignment->recalculateProgress();
        $assignment->refresh();

        $formIds     = $assignment->funnel->form_ids ?? [];
        $nextIndex   = $stepIndex + 1;
        $isLastStep  = $nextIndex >= count($formIds);

        return response()->json([
            'status'           => 'success',
            'message'          => $isLastStep ? 'All done! Thank you.' : 'Step saved. Moving to next form.',
            'is_last_step'     => $isLastStep,
            'next_step_index'  => $isLastStep ? null : $nextIndex,
            'progress_percent' => $assignment->progress_percent,
            'forms_completed'  => $assignment->forms_completed,
            'forms_total'      => $assignment->forms_total,
        ]);
    }

    /**
     * Mark the entire funnel as complete
     * Route: POST /fill/{token}/complete
     */
    public function complete(Request $request, string $token)
    {
        $assignment = PatientFunnelAssignment::where('token', $token)->firstOrFail();
        $assignment->recalculateProgress();

        return response()->json([
            'status'  => 'success',
            'message' => 'Funnel completed. Thank you!',
        ]);
    }
}
