<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Form;
use App\Models\Funnel;
use App\Models\FormSubmission;
use App\Models\Message;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_patients' => Patient::count(),
            'active_patients' => Patient::where('status', 'active')->count(),
            'total_forms' => Form::count(),
            'active_forms' => Form::where('status', 'active')->count(),
            'total_funnels' => Funnel::count(),
            'active_funnels' => Funnel::where('status', 'active')->count(),
            'total_submissions' => FormSubmission::count(),
            'pending_submissions' => FormSubmission::where('status', 'pending')->count(),
            'unread_messages' => Message::where('status', 'unread')->count(),
            'new_patients_this_month' => Patient::whereMonth('created_at', now()->month)->count(),
        ];

        $recentPatients = Patient::latest()->take(5)->get();
        $recentSubmissions = FormSubmission::with(['form', 'patient'])->latest()->take(5)->get();
        $recentMessages = Message::with('patient')->where('status', 'unread')->latest()->take(5)->get();

        return view('dashboard', compact('stats', 'recentPatients', 'recentSubmissions', 'recentMessages'));
    }
}
