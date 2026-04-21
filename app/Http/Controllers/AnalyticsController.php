<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Funnel;
use App\Models\Patient; 
use App\Models\PatientFunnelAssignment;
use App\Models\FormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function __construct()
    { 
        $this->middleware('auth');
    }

    /**
     * Funnel Analytics — per-funnel stats with patient breakdown
     */
    public function funnels()
    {
        // Overall summary
        $summary = [
            'total_funnels'     => Funnel::count(),
            'total_assignments' => PatientFunnelAssignment::count(),
            'not_started'       => PatientFunnelAssignment::where('status', 'pending')->count(),
            'in_progress'       => PatientFunnelAssignment::where('status', 'in_progress')->count(),
            'completed'         => PatientFunnelAssignment::where('status', 'completed')->count(),
        ];

        // Per-funnel data
        $funnels = Funnel::withCount([
            'assignments',
        ])->orderBy('created_at', 'desc')->get();

        foreach ($funnels as $funnel) {
            // Count form_ids
            $formIds = is_array($funnel->form_ids) ? $funnel->form_ids : (json_decode($funnel->form_ids ?? '[]', true) ?: []);
            $funnel->form_count = count($formIds);

            // Stats per status
            $assignments = PatientFunnelAssignment::where('funnel_id', $funnel->id)
                ->with('patient')
                ->get();

            $funnel->stats = [
                'total'        => $assignments->count(),
                'completed'    => $assignments->where('status', 'completed')->count(),
                'in_progress'  => $assignments->where('status', 'in_progress')->count(),
                'not_started'  => $assignments->where('status', 'pending')->count(),
                'expired'      => $assignments->where('status', 'expired')->count(),
                'avg_progress' => $assignments->count() > 0
                    ? round($assignments->avg('progress_percent'))
                    : 0,
            ];

            $funnel->assignments = $assignments->sortByDesc('created_at');
        }

        return view('analytics.funnels', compact('funnels', 'summary'));
    }

    /**
     * Form Analytics — per-form submission stats with patient breakdown
     */
    public function forms()
    {
        $allSubmissions = FormSubmission::count();
        $allDrafts      = FormSubmission::where('status', 'draft')->count();

        $summary = [
            'total_forms'          => Form::count(),
            'total_submissions'    => $allSubmissions,
            'total_drafts'         => $allDrafts,
            'active_forms'         => Form::where('status', 'active')->count(),
            'avg_completion_rate'  => $allSubmissions > 0
                ? round((($allSubmissions - $allDrafts) / $allSubmissions) * 100)
                : 0,
        ];

        $forms = Form::orderBy('created_at', 'desc')->get();

        foreach ($forms as $form) {
            // Count fields
            $fields = is_array($form->fields) ? $form->fields : (json_decode($form->fields ?? '[]', true) ?: []);
            $form->field_count = count($fields);

            // Submission stats
            $submissions = FormSubmission::where('form_id', $form->id)->get();
            $completed   = $submissions->where('status', '!=', 'draft')->count();
            $drafts      = $submissions->where('status', 'draft')->count();

            $form->stats = [
                'total_submissions' => $submissions->count(),
                'completed'         => $completed,
                'drafts'            => $drafts,
                'unique_patients'   => $submissions->whereNotNull('patient_id')->pluck('patient_id')->unique()->count(),
            ];

            // Recent 5 submissions with patient
            $form->recentSubmissions = FormSubmission::where('form_id', $form->id)
                ->with('patient')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }

        return view('analytics.forms', compact('forms', 'summary'));
    }

    /**
     * Reports Overview — high-level summary with charts data
     */
    public function reports(Request $request)
    {
        $from = $request->input('from', now()->subDays(30)->format('Y-m-d'));
        $to   = $request->input('to', now()->format('Y-m-d'));

        $fromDate = \Carbon\Carbon::parse($from)->startOfDay();
        $toDate   = \Carbon\Carbon::parse($to)->endOfDay();

        // Overall counts
        $totalPatients    = Patient::count();
        $newPatients      = Patient::whereBetween('created_at', [$fromDate, $toDate])->count();
        $totalAssignments = PatientFunnelAssignment::count();
        $newAssignments   = PatientFunnelAssignment::whereBetween('created_at', [$fromDate, $toDate])->count();
        $totalSubmissions = FormSubmission::count();
        $newSubmissions   = FormSubmission::whereBetween('created_at', [$fromDate, $toDate])->count();

        // Completion rate
        $completedAssignments = PatientFunnelAssignment::where('status', 'completed')->count();
        $overallRate = $totalAssignments > 0
            ? round(($completedAssignments / $totalAssignments) * 100)
            : 0;

        // Assignments by status
        $byStatus = PatientFunnelAssignment::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $assignmentsByStatus = [
            'total'       => $totalAssignments,
            'completed'   => $byStatus['completed'] ?? 0,
            'in_progress' => $byStatus['in_progress'] ?? 0,
            'pending'     => $byStatus['pending'] ?? 0,
            'expired'     => $byStatus['expired'] ?? 0,
        ];

        // Submissions by status
        $allSubs   = FormSubmission::count();
        $draftSubs = FormSubmission::where('status', 'draft')->count();

        $submissionsByStatus = [
            'total'     => $allSubs,
            'completed' => $allSubs - $draftSubs,
            'drafts'    => $draftSubs,
        ];

        // Top forms by submissions
        $topForms = Form::withCount('submissions')
            ->orderBy('submissions_count', 'desc')
            ->take(5)
            ->get();

        // Most active patients
        $topPatients = Patient::withCount(['submissions', 'assignments'])
            ->orderBy('submissions_count', 'desc')
            ->take(8)
            ->get();

        // Needs attention — in_progress or pending assignments, not completed, ordered by oldest last activity
        $needsAttention = PatientFunnelAssignment::whereIn('status', ['pending', 'in_progress'])
            ->with(['patient', 'funnel'])
            ->orderBy('last_accessed_at', 'asc')
            ->take(8)
            ->get();

        $stats = [
            'total_patients'          => $totalPatients,
            'new_patients'            => $newPatients,
            'total_assignments'       => $totalAssignments,
            'new_assignments'         => $newAssignments,
            'total_submissions'       => $totalSubmissions,
            'new_submissions'         => $newSubmissions,
            'overall_completion_rate' => $overallRate,
            'assignments_by_status'   => $assignmentsByStatus,
            'submissions_by_status'   => $submissionsByStatus,
            'top_forms'               => $topForms,
            'top_patients'            => $topPatients,
            'needs_attention'         => $needsAttention,
        ];

        return view('analytics.reports', compact('stats', 'from', 'to'));
    }
}
