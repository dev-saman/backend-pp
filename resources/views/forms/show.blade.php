@extends('layouts.app')

@section('title', $form->name . ' - AdvantageHCS Admin')
@section('page-title', $form->name)
@section('page-subtitle', 'Form Builder')

@section('header-actions')
    <a href="{{ route('forms.edit', $form) }}" class="btn btn-secondary">
        <i class="fas fa-cog"></i> Settings
    </a>
    <a href="{{ route('forms.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
@endsection

@section('content')
<!-- Form Meta Bar -->
<div style="display:flex; gap:12px; align-items:center; margin-bottom:20px;">
    <span class="badge {{ $form->status === 'active' ? 'badge-success' : ($form->status === 'draft' ? 'badge-warning' : 'badge-secondary') }}" style="font-size:13px; padding:6px 12px;">
        {{ ucfirst($form->status) }}
    </span>
    @if($form->category)
        <span style="font-size:13px; color:#6b7280;"><i class="fas fa-tag" style="margin-right:4px;"></i>{{ ucfirst($form->category) }}</span>
    @endif
    <span style="font-size:13px; color:#6b7280;"><i class="fas fa-inbox" style="margin-right:4px;"></i>{{ $form->submission_count }} submissions</span>
    <span style="font-size:13px; color:#6b7280;"><i class="fas fa-clock" style="margin-right:4px;"></i>Created {{ $form->created_at->format('M d, Y') }}</span>
</div>

<!-- Black Builder Canvas -->
<div style="
    background: #000000;
    border-radius: 12px;
    min-height: 600px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 48px;
    position: relative;
    overflow: hidden;
">
    <!-- Subtle grid pattern -->
    <div style="
        position: absolute;
        inset: 0;
        background-image: radial-gradient(circle, #333 1px, transparent 1px);
        background-size: 32px 32px;
        opacity: 0.3;
    "></div>

    <div style="position:relative; text-align:center; max-width:480px;">
        <div style="
            width: 72px;
            height: 72px;
            background: rgba(200,16,46,0.15);
            border: 2px solid rgba(200,16,46,0.4);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        ">
            <i class="fas fa-puzzle-piece" style="font-size:28px; color:#C8102E;"></i>
        </div>

        <h2 style="color:#ffffff; font-size:22px; font-weight:700; margin-bottom:12px;">
            Form Builder
        </h2>
        <p style="color:#9ca3af; font-size:15px; line-height:1.6; margin-bottom:32px;">
            The drag-and-drop form builder will be implemented here. You'll be able to add fields, configure validation, set conditional logic, and preview the form in real time.
        </p>

        <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
            <div style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:8px; padding:12px 20px; color:#d1d5db; font-size:13px;">
                <i class="fas fa-text-height" style="margin-right:8px; color:#60a5fa;"></i>Text Field
            </div>
            <div style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:8px; padding:12px 20px; color:#d1d5db; font-size:13px;">
                <i class="fas fa-check-square" style="margin-right:8px; color:#34d399;"></i>Checkbox
            </div>
            <div style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:8px; padding:12px 20px; color:#d1d5db; font-size:13px;">
                <i class="fas fa-dot-circle" style="margin-right:8px; color:#f59e0b;"></i>Radio
            </div>
            <div style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:8px; padding:12px 20px; color:#d1d5db; font-size:13px;">
                <i class="fas fa-calendar" style="margin-right:8px; color:#a78bfa;"></i>Date
            </div>
            <div style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:8px; padding:12px 20px; color:#d1d5db; font-size:13px;">
                <i class="fas fa-signature" style="margin-right:8px; color:#f87171;"></i>Signature
            </div>
        </div>

        <div style="margin-top:40px; padding:16px; background:rgba(200,16,46,0.1); border:1px solid rgba(200,16,46,0.3); border-radius:8px;">
            <p style="color:#fca5a5; font-size:13px; margin:0;">
                <i class="fas fa-info-circle" style="margin-right:6px;"></i>
                Coming Soon — Drag-and-drop form builder with field configuration, conditional logic, and patient submission tracking.
            </p>
        </div>
    </div>
</div>

<!-- Submissions Table -->
@if($form->submissions->isNotEmpty())
<div class="card" style="margin-top:24px;">
    <div class="card-header">
        <span class="card-title">Form Submissions</span>
        <span style="font-size:13px; color:#6b7280;">{{ $form->submissions->count() }} total</span>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($form->submissions as $submission)
                <tr>
                    <td style="font-weight:500;">{{ $submission->patient->full_name ?? 'Anonymous' }}</td>
                    <td>
                        <span class="badge {{ $submission->status === 'reviewed' ? 'badge-success' : ($submission->status === 'pending' ? 'badge-warning' : 'badge-secondary') }}">
                            {{ ucfirst($submission->status) }}
                        </span>
                    </td>
                    <td style="font-size:13px; color:#6b7280;">{{ $submission->created_at->format('M d, Y h:i A') }}</td>
                    <td>
                        <button class="btn btn-secondary btn-sm" onclick="alert('Submission data viewer coming soon')">
                            <i class="fas fa-eye"></i> View
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
