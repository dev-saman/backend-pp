<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\FunnelController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AnalyticsController;

// ============================================================
// PUBLIC ROUTES — No authentication required
// ============================================================

// Standalone public form (not assigned to a patient)
Route::get('/f/{slug}', [FormController::class, 'publicForm'])->name('forms.public');
Route::post('/f/{slug}/submit', [FormController::class, 'submitPublicForm'])->name('forms.submit');

// Public funnel (not assigned to a patient)
Route::get('/funnel/{slug}', [FunnelController::class, 'publicFunnel'])->name('funnels.public');
Route::post('/funnel/{slug}/submit', [FunnelController::class, 'submitPublicFunnel'])->name('funnels.submit');

// Patient-specific funnel link (assigned, tracks progress per patient)
// URL: /fill/{token}
Route::get('/fill/{token}', [AssignmentController::class, 'fillFunnel'])->name('assignments.fill');
Route::post('/fill/{token}/save', [AssignmentController::class, 'autoSave'])->name('assignments.autosave');
Route::post('/fill/{token}/submit-step', [AssignmentController::class, 'submitStep'])->name('assignments.submit-step');
Route::post('/fill/{token}/complete', [AssignmentController::class, 'complete'])->name('assignments.complete');

// ============================================================
// AUTH ROUTES
// ============================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ============================================================
// ADMIN ROUTES — Protected by auth middleware
// ============================================================
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // ---- Patients (read-only — data comes from external AHCS database) ----
    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');

    // ---- Appointments (read-only, fetched from external system) ----
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{id}', [AppointmentController::class, 'show'])->name('appointments.show');

    // ---- Forms ----
    Route::resource('forms', FormController::class);
    Route::get('/forms/{form}/builder', [FormController::class, 'builder'])->name('forms.builder');
    // AJAX: Save form field schema from builder
    Route::post('/forms/{form}/schema', [FormController::class, 'saveSchema'])->name('forms.schema');
    // AJAX: Publish form
    Route::post('/forms/{form}/publish', [FormController::class, 'publish'])->name('forms.publish');
    Route::get('/forms/{form}/public-url', [FormController::class, 'getPublicUrl'])->name('forms.public-url');

    // ---- Funnels ----
    Route::resource('funnels', FunnelController::class);
    // AJAX: Save funnel form_ids from builder
    Route::post('/funnels/{funnel}/schema', [FunnelController::class, 'saveSchema'])->name('funnels.schema');
    Route::post('/funnels/{funnel}/publish', [FunnelController::class, 'publish'])->name('funnels.publish');

    // ---- Assignments (assign funnel to patient) ----
    Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
    Route::post('/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
    Route::delete('/assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');
    Route::get('/assignments/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');
    // Resend link to patient
    Route::post('/assignments/{assignment}/resend', [AssignmentController::class, 'resend'])->name('assignments.resend');

    // ---- Progress Tracking (redirects to Assignments — merged) ----
    Route::get('/progress', function() { return redirect()->route('assignments.index'); })->name('progress.index');
    Route::get('/progress/patient/{patient}', [AssignmentController::class, 'patientProgress'])->name('progress.patient');
    Route::get('/progress/funnel/{funnel}', [AssignmentController::class, 'funnelProgress'])->name('progress.funnel');

    // ---- Analytics & Reports ----
    Route::get('/analytics/funnels', [AnalyticsController::class, 'funnels'])->name('analytics.funnels');
    Route::get('/analytics/forms', [AnalyticsController::class, 'forms'])->name('analytics.forms');
    Route::get('/analytics/reports', [AnalyticsController::class, 'reports'])->name('analytics.reports');

    // ---- Billing (read-only, fetched from external system) ----
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::get('/billing/{id}', [BillingController::class, 'show'])->name('billing.show');

    // ---- Messages ----
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{message}/reply', [MessageController::class, 'reply'])->name('messages.reply');
});
