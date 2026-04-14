@extends('layouts.app')

@section('title', 'Funnel Analytics')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Funnel Analytics</h1>
        <p class="page-subtitle">Completion statistics for every funnel — see who finished, who's in progress, and who hasn't started</p>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('analytics.forms') }}" class="btn btn-secondary">Form Analytics</a>
        <a href="{{ route('analytics.reports') }}" class="btn btn-primary">Reports Overview</a>
    </div>
</div>

{{-- Top Summary Stats --}}
<div class="stats-grid" style="grid-template-columns:repeat(5,1fr);margin-bottom:28px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff;color:#3b82f6;">🔀</div>
        <div class="stat-info">
            <div class="stat-value">{{ $summary['total_funnels'] }}</div>
            <div class="stat-label">Total Funnels</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f0fdf4;color:#22c55e;">📋</div>
        <div class="stat-info">
            <div class="stat-value">{{ $summary['total_assignments'] }}</div>
            <div class="stat-label">Total Assigned</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef9c3;color:#ca8a04;">⏳</div>
        <div class="stat-info">
            <div class="stat-value">{{ $summary['not_started'] }}</div>
            <div class="stat-label">Not Started</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fff7ed;color:#ea580c;">🔄</div>
        <div class="stat-info">
            <div class="stat-value">{{ $summary['in_progress'] }}</div>
            <div class="stat-label">In Progress</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f0fdf4;color:#22c55e;">✅</div>
        <div class="stat-info">
            <div class="stat-value">{{ $summary['completed'] }}</div>
            <div class="stat-label">Completed</div>
        </div>
    </div>
</div>

{{-- Per-Funnel Cards --}}
@forelse($funnels as $funnel)
<div class="card" style="margin-bottom:24px;">
    {{-- Funnel Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #f1f5f9;">
        <div style="display:flex;align-items:center;gap:14px;">
            <div style="width:44px;height:44px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;">🔀</div>
            <div>
                <div style="font-size:17px;font-weight:700;color:#1e293b;">{{ $funnel->name }}</div>
                <div style="font-size:12px;color:#94a3b8;margin-top:2px;">
                    {{ $funnel->form_count }} form{{ $funnel->form_count != 1 ? 's' : '' }} in funnel
                    &nbsp;·&nbsp;
                    <span style="color:{{ $funnel->status === 'active' ? '#22c55e' : '#f59e0b' }};">{{ ucfirst($funnel->status) }}</span>
                    &nbsp;·&nbsp; Created {{ $funnel->created_at->format('M d, Y') }}
                </div>
            </div>
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            <a href="{{ route('progress.funnel', $funnel->id) }}" class="btn btn-sm btn-secondary">View All Patients</a>
            <a href="{{ route('funnels.edit', $funnel->id) }}" class="btn btn-sm btn-secondary">Edit Funnel</a>
        </div>
    </div>

    {{-- Stats Row --}}
    <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:0;border-bottom:1px solid #f1f5f9;">
        @php
            $total = $funnel->stats['total'];
            $completed = $funnel->stats['completed'];
            $in_progress = $funnel->stats['in_progress'];
            $not_started = $funnel->stats['not_started'];
            $expired = $funnel->stats['expired'];
            $avg = $funnel->stats['avg_progress'];
            $rate = $total > 0 ? round(($completed / $total) * 100) : 0;
        @endphp
        <div style="padding:16px 20px;border-right:1px solid #f1f5f9;text-align:center;">
            <div style="font-size:24px;font-weight:800;color:#1e293b;">{{ $total }}</div>
            <div style="font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-top:2px;">Assigned</div>
        </div>
        <div style="padding:16px 20px;border-right:1px solid #f1f5f9;text-align:center;">
            <div style="font-size:24px;font-weight:800;color:#22c55e;">{{ $completed }}</div>
            <div style="font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-top:2px;">Completed</div>
        </div>
        <div style="padding:16px 20px;border-right:1px solid #f1f5f9;text-align:center;">
            <div style="font-size:24px;font-weight:800;color:#3b82f6;">{{ $in_progress }}</div>
            <div style="font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-top:2px;">In Progress</div>
        </div>
        <div style="padding:16px 20px;border-right:1px solid #f1f5f9;text-align:center;">
            <div style="font-size:24px;font-weight:800;color:#f59e0b;">{{ $not_started }}</div>
            <div style="font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-top:2px;">Not Started</div>
        </div>
        <div style="padding:16px 20px;border-right:1px solid #f1f5f9;text-align:center;">
            <div style="font-size:24px;font-weight:800;color:#ef4444;">{{ $expired }}</div>
            <div style="font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-top:2px;">Expired</div>
        </div>
        <div style="padding:16px 20px;text-align:center;">
            <div style="font-size:24px;font-weight:800;color:#6366f1;">{{ $rate }}%</div>
            <div style="font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-top:2px;">Completion Rate</div>
        </div>
    </div>

    {{-- Progress Bar Visual --}}
    @if($total > 0)
    <div style="padding:16px 24px;border-bottom:1px solid #f1f5f9;">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
            <span style="font-size:12px;font-weight:600;color:#64748b;">Overall Progress Distribution</span>
            <span style="font-size:11px;color:#94a3b8;">Avg. {{ $avg }}% complete per patient</span>
        </div>
        <div style="height:12px;background:#f1f5f9;border-radius:6px;overflow:hidden;display:flex;">
            @if($completed > 0)
            <div style="width:{{ ($completed/$total)*100 }}%;background:#22c55e;height:100%;" title="{{ $completed }} Completed"></div>
            @endif
            @if($in_progress > 0)
            <div style="width:{{ ($in_progress/$total)*100 }}%;background:#3b82f6;height:100%;" title="{{ $in_progress }} In Progress"></div>
            @endif
            @if($not_started > 0)
            <div style="width:{{ ($not_started/$total)*100 }}%;background:#e2e8f0;height:100%;" title="{{ $not_started }} Not Started"></div>
            @endif
            @if($expired > 0)
            <div style="width:{{ ($expired/$total)*100 }}%;background:#fca5a5;height:100%;" title="{{ $expired }} Expired"></div>
            @endif
        </div>
        <div style="display:flex;gap:16px;margin-top:8px;">
            <span style="font-size:11px;color:#22c55e;display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;background:#22c55e;border-radius:50%;display:inline-block;"></span>Completed</span>
            <span style="font-size:11px;color:#3b82f6;display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;background:#3b82f6;border-radius:50%;display:inline-block;"></span>In Progress</span>
            <span style="font-size:11px;color:#94a3b8;display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;background:#e2e8f0;border-radius:50%;display:inline-block;"></span>Not Started</span>
            <span style="font-size:11px;color:#ef4444;display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;background:#fca5a5;border-radius:50%;display:inline-block;"></span>Expired</span>
        </div>
    </div>

    {{-- Patient Breakdown Table --}}
    <div style="padding:0;">
        <div style="padding:12px 24px;background:#f8fafc;border-bottom:1px solid #f1f5f9;">
            <span style="font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.5px;">Patient Breakdown</span>
        </div>
        <div class="table-responsive">
            <table class="table" style="margin:0;">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Assigned</th>
                        <th>Progress</th>
                        <th>Forms Done</th>
                        <th>Status</th>
                        <th>Last Activity</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($funnel->assignments->take(5) as $a)
                    @php
                        $sc = ['pending'=>['bg'=>'#fef9c3','color'=>'#ca8a04','label'=>'Not Started'],
                               'in_progress'=>['bg'=>'#dbeafe','color'=>'#2563eb','label'=>'In Progress'],
                               'completed'=>['bg'=>'#dcfce7','color'=>'#16a34a','label'=>'Completed'],
                               'expired'=>['bg'=>'#fee2e2','color'=>'#dc2626','label'=>'Expired']];
                        $s = $sc[$a->status] ?? ['bg'=>'#f1f5f9','color'=>'#64748b','label'=>ucfirst($a->status)];
                    @endphp
                    <tr>
                        <td>
                            <div style="font-weight:600;color:#1e293b;">{{ $a->patient->first_name }} {{ $a->patient->last_name }}</div>
                            <div style="font-size:12px;color:#94a3b8;">{{ $a->patient->email }}</div>
                        </td>
                        <td style="font-size:13px;color:#64748b;">{{ $a->created_at->format('M d, Y') }}</td>
                        <td style="min-width:160px;">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="flex:1;height:6px;background:#e2e8f0;border-radius:3px;overflow:hidden;">
                                    <div style="height:100%;width:{{ $a->progress_percent }}%;background:{{ $a->progress_percent==100 ? '#22c55e' : '#3b82f6' }};border-radius:3px;"></div>
                                </div>
                                <span style="font-size:12px;font-weight:700;color:#374151;min-width:32px;">{{ $a->progress_percent }}%</span>
                            </div>
                        </td>
                        <td style="font-size:13px;color:#374151;font-weight:600;">{{ $a->forms_completed }}/{{ $a->forms_total }}</td>
                        <td>
                            <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $s['bg'] }};color:{{ $s['color'] }};">
                                {{ $s['label'] }}
                            </span>
                        </td>
                        <td style="font-size:12px;color:#94a3b8;">{{ $a->last_accessed_at ? $a->last_accessed_at->diffForHumans() : 'Never' }}</td>
                        <td>
                            <a href="{{ route('assignments.show', $a->id) }}" style="font-size:12px;color:#6366f1;font-weight:600;">Details →</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($funnel->assignments->count() > 5)
        <div style="padding:12px 24px;text-align:center;border-top:1px solid #f1f5f9;">
            <a href="{{ route('progress.funnel', $funnel->id) }}" style="font-size:13px;color:#6366f1;font-weight:600;">
                View all {{ $funnel->assignments->count() }} patients for this funnel →
            </a>
        </div>
        @endif
    </div>
    @else
    <div style="padding:32px;text-align:center;color:#94a3b8;">
        <div style="font-size:32px;margin-bottom:8px;">👥</div>
        <div style="font-weight:600;margin-bottom:4px;">No patients assigned yet</div>
        <a href="{{ route('assignments.index') }}" style="font-size:13px;color:#6366f1;">Assign this funnel to patients →</a>
    </div>
    @endif
</div>
@empty
<div class="card" style="padding:64px;text-align:center;">
    <div style="font-size:48px;margin-bottom:16px;">🔀</div>
    <div style="font-size:18px;font-weight:700;color:#1e293b;margin-bottom:8px;">No funnels created yet</div>
    <p style="color:#94a3b8;margin-bottom:20px;">Create your first funnel to start tracking patient progress.</p>
    <a href="{{ route('funnels.create') }}" class="btn btn-primary">Create Funnel</a>
</div>
@endforelse
@endsection
