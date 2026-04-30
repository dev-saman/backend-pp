<?php
namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Funnel;
use App\Models\FormSubmission;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Funnel Analytics — per-funnel stats with submission breakdown
     */
    public function funnels()
    {
        $summary = [
            'total_funnels'     => Funnel::count(),
            'total_submissions' => FormSubmission::whereNotNull('funnel_id')->count(),
            'not_started'       => 0,
            'in_progress'       => FormSubmission::where('status', 'draft')->whereNotNull('funnel_id')->count(),
            'completed'         => FormSubmission::where('status', '!=', 'draft')->whereNotNull('funnel_id')->count(),
        ];

        $funnels = Funnel::orderBy('created_at', 'desc')->get();

        foreach ($funnels as $funnel) {
            $formIds = is_array($funnel->form_ids) ? $funnel->form_ids : (json_decode($funnel->form_ids ?? '[]', true) ?: []);
            $funnel->form_count = count($formIds);

            $submissions = FormSubmission::where('funnel_id', $funnel->id)->get();
            $completed   = $submissions->where('status', '!=', 'draft')->count();
            $inProgress  = $submissions->where('status', 'draft')->count();
            $total       = $submissions->count();

            $funnel->stats = [
                'total'        => $total,
                'completed'    => $completed,
                'in_progress'  => $inProgress,
                'not_started'  => 0,
                'expired'      => 0,
                'avg_progress' => 0,
            ];
            $funnel->recentSubmissions = $submissions->sortByDesc('created_at')->take(5);
        }

        return view('analytics.funnels', compact('funnels', 'summary'));
    }

    /**
     * Form Analytics — per-form submission stats
     */
    public function forms()
    {
        $allSubmissions = FormSubmission::count();
        $allDrafts      = FormSubmission::where('status', 'draft')->count();

        $summary = [
            'total_forms'         => Form::count(),
            'total_submissions'   => $allSubmissions,
            'total_drafts'        => $allDrafts,
            'active_forms'        => Form::where('is_active', 1)->count(),
            'avg_completion_rate' => $allSubmissions > 0
                ? round((($allSubmissions - $allDrafts) / $allSubmissions) * 100)
                : 0,
        ];

        $forms = Form::orderBy('created_at', 'desc')->get();

        foreach ($forms as $form) {
            $fields = is_array($form->fields) ? $form->fields : (json_decode($form->fields ?? '[]', true) ?: []);
            $form->field_count = count($fields);

            $submissions = FormSubmission::where('form_id', $form->id)->get();
            $completed   = $submissions->where('status', '!=', 'draft')->count();
            $drafts      = $submissions->where('status', 'draft')->count();

            $form->stats = [
                'total_submissions' => $submissions->count(),
                'completed'         => $completed,
                'drafts'            => $drafts,
            ];

            $form->recentSubmissions = FormSubmission::where('form_id', $form->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }

        return view('analytics.forms', compact('forms', 'summary'));
    }

    /**
     * Reports Overview — high-level summary
     */
    public function reports(Request $request)
    {
        $from = $request->input('from', now()->subDays(30)->format('Y-m-d'));
        $to   = $request->input('to', now()->format('Y-m-d'));
        $fromDate = \Carbon\Carbon::parse($from)->startOfDay();
        $toDate   = \Carbon\Carbon::parse($to)->endOfDay();

        $totalSubmissions = FormSubmission::count();
        $newSubmissions   = FormSubmission::whereBetween('created_at', [$fromDate, $toDate])->count();
        $allSubs          = FormSubmission::count();
        $draftSubs        = FormSubmission::where('status', 'draft')->count();

        $submissionsByStatus = [
            'total'     => $allSubs,
            'completed' => $allSubs - $draftSubs,
            'drafts'    => $draftSubs,
        ];

        $topForms = Form::withCount('submissions')
            ->orderBy('submissions_count', 'desc')
            ->take(5)
            ->get();

        $topFunnels = Funnel::withCount('submissions')
            ->orderBy('submissions_count', 'desc')
            ->take(5)
            ->get();

        $stats = [
            'total_submissions'     => $totalSubmissions,
            'new_submissions'       => $newSubmissions,
            'total_forms'           => Form::count(),
            'active_forms'          => Form::where('is_active', 1)->count(),
            'total_funnels'         => Funnel::count(),
            'active_funnels'        => Funnel::where('status', 'active')->count(),
            'submissions_by_status' => $submissionsByStatus,
            'top_forms'             => $topForms,
            'top_funnels'           => $topFunnels,
        ];

        return view('analytics.reports', compact('stats', 'from', 'to'));
    }
}
