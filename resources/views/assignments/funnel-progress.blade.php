@extends('layouts.app')

@section('title', 'Funnel Progress')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $funnel->name }} — Patient Progress</h1>
        <p class="page-subtitle">
            <a href="{{ route('funnels.index') }}" style="color:#3b82f6;">Funnels</a> →
            {{ $funnel->name }}
        </p>
    </div>
</div>

@php
    $total      = $assignments->count();
    $completed  = $assignments->where('status','completed')->count();
    $inProgress = $assignments->where('status','in_progress')->count();
    $pending    = $assignments->where('status','pending')->count();
    $avgPct     = $total > 0 ? round($assignments->avg('progress_percent')) : 0;
@endphp

<div class="stats-grid" style="grid-template-columns:repeat(5,1fr);margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff;color:#3b82f6;">👥</div>
        <div class="stat-info"><div class="stat-value">{{ $total }}</div><div class="stat-label">Patients Assigned</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef9c3;color:#ca8a04;">⏳</div>
        <div class="stat-info"><div class="stat-value">{{ $pending }}</div><div class="stat-label">Not Started</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fff7ed;color:#ea580c;">🔄</div>
        <div class="stat-info"><div class="stat-value">{{ $inProgress }}</div><div class="stat-label">In Progress</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f0fdf4;color:#22c55e;">✅</div>
        <div class="stat-info"><div class="stat-value">{{ $completed }}</div><div class="stat-label">Completed</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f5f3ff;color:#8b5cf6;">📊</div>
        <div class="stat-info"><div class="stat-value">{{ $avgPct }}%</div><div class="stat-label">Avg. Completion</div></div>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Progress</th>
                    <th>Status</th>
                    <th>Assigned</th>
                    <th>Last Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignments as $a)
                @php
                    $statusColors = ['pending'=>'#f59e0b','in_progress'=>'#3b82f6','completed'=>'#22c55e','expired'=>'#ef4444'];
                    $statusLabels = ['pending'=>'Not Started','in_progress'=>'In Progress','completed'=>'Completed','expired'=>'Expired'];
                @endphp
                <tr>
                    <td>
                        <div style="font-weight:600;color:#1e293b;">{{ $a->patient->first_name }} {{ $a->patient->last_name }}</div>
                        <div style="font-size:12px;color:#94a3b8;">{{ $a->patient->email }}</div>
                    </td>
                    <td style="min-width:180px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="flex:1;height:6px;background:#e2e8f0;border-radius:3px;overflow:hidden;">
                                <div style="height:100%;width:{{ $a->progress_percent }}%;background:{{ $a->progress_percent==100 ? '#22c55e' : '#3b82f6' }};border-radius:3px;"></div>
                            </div>
                            <span style="font-size:12px;font-weight:700;color:#374151;">{{ $a->progress_percent }}%</span>
                        </div>
                        <div style="font-size:11px;color:#94a3b8;margin-top:2px;">{{ $a->forms_completed }}/{{ $a->forms_total }} forms</div>
                    </td>
                    <td>
                        <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;background:{{ $statusColors[$a->status] ?? '#94a3b8' }}20;color:{{ $statusColors[$a->status] ?? '#94a3b8' }};">
                            {{ $statusLabels[$a->status] ?? ucfirst($a->status) }}
                        </span>
                    </td>
                    <td style="font-size:13px;color:#64748b;">{{ $a->created_at->format('M j, Y') }}</td>
                    <td style="font-size:13px;color:#64748b;">{{ $a->last_accessed_at ? $a->last_accessed_at->diffForHumans() : 'Never' }}</td>
                    <td>
                        <a href="{{ route('assignments.show', $a) }}" class="btn btn-sm btn-secondary">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:48px;color:#94a3b8;">No patients assigned to this funnel yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
