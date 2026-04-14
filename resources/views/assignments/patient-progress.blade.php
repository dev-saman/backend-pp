@extends('layouts.app')

@section('title', 'Patient Progress')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $patient->first_name }} {{ $patient->last_name }} — Progress</h1>
        <p class="page-subtitle">
            <a href="{{ route('patients.show', $patient) }}" style="color:#3b82f6;">Patient Profile</a> →
            Funnel Progress
        </p>
    </div>
    <a href="{{ route('assignments.index') }}" class="btn btn-secondary">+ Assign New Funnel</a>
</div>

@if($assignments->isEmpty())
<div class="card" style="text-align:center;padding:60px;">
    <div style="font-size:48px;margin-bottom:16px;">📋</div>
    <h3 style="font-size:18px;font-weight:700;color:#1e293b;margin-bottom:8px;">No Funnels Assigned</h3>
    <p style="color:#64748b;margin-bottom:24px;">This patient has not been assigned any funnels yet.</p>
    <a href="{{ route('assignments.index') }}" class="btn btn-primary">Assign a Funnel</a>
</div>
@else

@foreach($assignments as $assignment)
@php
    $formIds     = $assignment->funnel->form_ids ?? [];
    $progressMap = $assignment->progress->keyBy('form_id');
    $statusColors = ['pending'=>'#f59e0b','in_progress'=>'#3b82f6','completed'=>'#22c55e','expired'=>'#ef4444'];
    $statusLabels = ['pending'=>'Not Started','in_progress'=>'In Progress','completed'=>'Completed','expired'=>'Expired'];
@endphp
<div class="card" style="margin-bottom:20px;">
    <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
        <div>
            <div style="font-size:16px;font-weight:700;color:#1e293b;">{{ $assignment->funnel->name }}</div>
            <div style="font-size:12px;color:#94a3b8;margin-top:2px;">
                Assigned {{ $assignment->created_at->format('M j, Y') }}
                @if($assignment->assignedBy) by {{ $assignment->assignedBy->name }} @endif
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:12px;">
            <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;background:{{ $statusColors[$assignment->status] ?? '#94a3b8' }}20;color:{{ $statusColors[$assignment->status] ?? '#94a3b8' }};">
                {{ $statusLabels[$assignment->status] ?? ucfirst($assignment->status) }}
            </span>
            <a href="{{ route('assignments.show', $assignment) }}" class="btn btn-sm btn-secondary">View Detail</a>
        </div>
    </div>
    <div style="padding:20px 24px;">
        {{-- Overall progress --}}
        <div style="margin-bottom:20px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                <span style="font-size:13px;font-weight:600;color:#374151;">Overall Progress</span>
                <span style="font-size:13px;font-weight:700;color:{{ $assignment->progress_percent==100 ? '#22c55e' : '#3b82f6' }};">
                    {{ $assignment->progress_percent }}% ({{ $assignment->forms_completed }}/{{ $assignment->forms_total }} forms)
                </span>
            </div>
            <div style="height:8px;background:#e2e8f0;border-radius:4px;overflow:hidden;">
                <div style="height:100%;width:{{ $assignment->progress_percent }}%;background:{{ $assignment->progress_percent==100 ? '#22c55e' : 'linear-gradient(90deg,#3b82f6,#8b5cf6)' }};border-radius:4px;"></div>
            </div>
        </div>

        {{-- Step-by-step --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;">
            @foreach($formIds as $i => $formId)
            @php
                $progress = $progressMap->get($formId);
                $stepStatus = $progress?->status ?? 'pending';
                $stepIcon = match($stepStatus) { 'completed' => '✅', 'draft' => '💾', default => '⏳' };
                $stepColor = match($stepStatus) { 'completed' => '#22c55e', 'draft' => '#f59e0b', default => '#94a3b8' };
                $stepLabel = match($stepStatus) { 'completed' => 'Completed', 'draft' => 'In Progress', default => 'Not Started' };
            @endphp
            <div style="border:1.5px solid {{ $stepColor }}30;border-radius:8px;padding:12px;background:{{ $stepColor }}08;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                    <span style="font-size:16px;">{{ $stepIcon }}</span>
                    <span style="font-size:11px;font-weight:700;color:{{ $stepColor }};text-transform:uppercase;letter-spacing:.5px;">
                        Step {{ $i + 1 }}
                    </span>
                </div>
                <div style="font-size:13px;font-weight:600;color:#1e293b;margin-bottom:4px;">
                    Form #{{ $formId }}
                </div>
                <div style="font-size:11px;color:#64748b;">{{ $stepLabel }}</div>
                @if($progress?->submitted_at)
                <div style="font-size:11px;color:#22c55e;margin-top:4px;">
                    {{ $progress->submitted_at->format('M j, g:i A') }}
                </div>
                @elseif($progress?->last_saved_at)
                <div style="font-size:11px;color:#f59e0b;margin-top:4px;">
                    Saved {{ $progress->last_saved_at->diffForHumans() }}
                </div>
                @endif
            </div>
            @endforeach
        </div>

        @if($assignment->last_accessed_at)
        <div style="margin-top:16px;font-size:12px;color:#94a3b8;">
            Last accessed {{ $assignment->last_accessed_at->diffForHumans() }}
        </div>
        @endif
    </div>
</div>
@endforeach

@endif
@endsection
