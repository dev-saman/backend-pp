@extends('layouts.app')

@section('title', 'Progress Tracking')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Progress Tracking</h1>
        <p class="page-subtitle">Monitor patient funnel completion across all assignments</p>
    </div>
</div>

{{-- Stats --}}
<div class="stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff;color:#3b82f6;">📋</div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Total Assignments</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef9c3;color:#ca8a04;">⏳</div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['pending'] }}</div>
            <div class="stat-label">Not Started</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fff7ed;color:#ea580c;">🔄</div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['in_progress'] }}</div>
            <div class="stat-label">In Progress</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f0fdf4;color:#22c55e;">✅</div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['completed'] }}</div>
            <div class="stat-label">Completed</div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:20px;">
    <form method="GET" style="display:flex;gap:12px;align-items:flex-end;">
        <div>
            <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">Status</label>
            <select name="status" class="form-control" style="height:38px;min-width:160px;">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status')==='pending' ? 'selected' : '' }}>Not Started</option>
                <option value="in_progress" {{ request('status')==='in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ request('status')==='completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="height:38px;">Filter</button>
        <a href="{{ route('progress.index') }}" class="btn btn-secondary" style="height:38px;line-height:38px;padding:0 16px;">Clear</a>
    </form>
</div>

{{-- Table --}}
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Funnel</th>
                    <th>Progress</th>
                    <th>Status</th>
                    <th>Last Activity</th>
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
                        <div style="font-weight:600;color:#1e293b;">
                            {{ $a->patient->first_name }} {{ $a->patient->last_name }}
                        </div>
                        <div style="font-size:12px;color:#94a3b8;">{{ $a->patient->email }}</div>
                    </td>
                    <td>
                        <div style="font-weight:500;color:#374151;">{{ $a->funnel->name }}</div>
                    </td>
                    <td style="min-width:200px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="flex:1;height:8px;background:#e2e8f0;border-radius:4px;overflow:hidden;">
                                <div style="height:100%;width:{{ $a->progress_percent }}%;background:{{ $a->progress_percent==100 ? '#22c55e' : ($a->progress_percent > 0 ? '#3b82f6' : '#e2e8f0') }};border-radius:4px;transition:width .5s;"></div>
                            </div>
                            <span style="font-size:12px;font-weight:700;color:#374151;white-space:nowrap;min-width:36px;">
                                {{ $a->progress_percent }}%
                            </span>
                        </div>
                        <div style="font-size:11px;color:#94a3b8;margin-top:3px;">
                            {{ $a->forms_completed }}/{{ $a->forms_total }} forms done
                        </div>
                    </td>
                    <td>
                        <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;background:{{ $statusColors[$a->status] ?? '#94a3b8' }}20;color:{{ $statusColors[$a->status] ?? '#94a3b8' }};">
                            {{ $statusLabels[$a->status] ?? ucfirst($a->status) }}
                        </span>
                    </td>
                    <td style="font-size:13px;color:#64748b;">
                        {{ $a->last_accessed_at ? $a->last_accessed_at->diffForHumans() : 'Never opened' }}
                    </td>
                    <td>
                        <a href="{{ route('assignments.show', $a) }}" class="btn btn-sm btn-secondary">View Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:48px;color:#94a3b8;">
                        No assignments found. <a href="{{ route('assignments.index') }}" style="color:#3b82f6;">Go to Assignments</a> to assign funnels to patients.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px;border-top:1px solid #f1f5f9;">
        {{ $assignments->links() }}
    </div>
</div>
@endsection
