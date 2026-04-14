@extends('layouts.app')

@section('title', 'Reports Overview')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Reports Overview</h1>
        <p class="page-subtitle">High-level summary of all patient form and funnel activity</p>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('analytics.funnels') }}" class="btn btn-secondary">Funnel Analytics</a>
        <a href="{{ route('analytics.forms') }}" class="btn btn-secondary">Form Analytics</a>
    </div>
</div>

{{-- Date Range Filter --}}
<div class="card" style="margin-bottom:24px;padding:16px 20px;">
    <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
        <div>
            <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">From Date</label>
            <input type="date" name="from" value="{{ request('from', now()->subDays(30)->format('Y-m-d')) }}" class="form-control" style="height:38px;">
        </div>
        <div>
            <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">To Date</label>
            <input type="date" name="to" value="{{ request('to', now()->format('Y-m-d')) }}" class="form-control" style="height:38px;">
        </div>
        <button type="submit" class="btn btn-primary" style="height:38px;">Apply Filter</button>
        <a href="{{ route('analytics.reports') }}" class="btn btn-secondary" style="height:38px;line-height:38px;padding:0 16px;">Reset</a>
        <div style="margin-left:auto;display:flex;gap:8px;">
            <button type="button" onclick="setRange(7)" class="btn btn-sm btn-secondary">Last 7 days</button>
            <button type="button" onclick="setRange(30)" class="btn btn-sm btn-secondary">Last 30 days</button>
            <button type="button" onclick="setRange(90)" class="btn btn-sm btn-secondary">Last 90 days</button>
        </div>
    </form>
</div>

{{-- Big Stats Row --}}
<div class="stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px;">
    <div class="stat-card" style="border-left:4px solid #3b82f6;">
        <div class="stat-icon" style="background:#eff6ff;color:#3b82f6;">👥</div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['total_patients'] }}</div>
            <div class="stat-label">Total Patients</div>
            <div style="font-size:11px;color:#22c55e;margin-top:2px;">+{{ $stats['new_patients'] }} this period</div>
        </div>
    </div>
    <div class="stat-card" style="border-left:4px solid #6366f1;">
        <div class="stat-icon" style="background:#f5f3ff;color:#6366f1;">📋</div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['total_assignments'] }}</div>
            <div class="stat-label">Funnel Assignments</div>
            <div style="font-size:11px;color:#22c55e;margin-top:2px;">+{{ $stats['new_assignments'] }} this period</div>
        </div>
    </div>
    <div class="stat-card" style="border-left:4px solid #22c55e;">
        <div class="stat-icon" style="background:#f0fdf4;color:#22c55e;">✅</div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['total_submissions'] }}</div>
            <div class="stat-label">Form Submissions</div>
            <div style="font-size:11px;color:#22c55e;margin-top:2px;">+{{ $stats['new_submissions'] }} this period</div>
        </div>
    </div>
    <div class="stat-card" style="border-left:4px solid #f59e0b;">
        <div class="stat-icon" style="background:#fffbeb;color:#f59e0b;">📊</div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['overall_completion_rate'] }}%</div>
            <div class="stat-label">Overall Completion Rate</div>
            <div style="font-size:11px;color:#94a3b8;margin-top:2px;">Across all funnels</div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;">

    {{-- Funnel Status Breakdown --}}
    <div class="card">
        <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;">
            <h3 style="font-size:15px;font-weight:700;color:#1e293b;margin:0;">Funnel Assignment Status</h3>
            <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;">All time breakdown by status</p>
        </div>
        <div style="padding:24px;">
            @php
                $fTotal = $stats['assignments_by_status']['total'] ?: 1;
                $fCompleted = $stats['assignments_by_status']['completed'];
                $fInProgress = $stats['assignments_by_status']['in_progress'];
                $fPending = $stats['assignments_by_status']['pending'];
                $fExpired = $stats['assignments_by_status']['expired'];
            @endphp

            {{-- Donut-style visual using stacked bars --}}
            <div style="display:flex;height:24px;border-radius:12px;overflow:hidden;margin-bottom:20px;">
                @if($fCompleted > 0)<div style="flex:{{ $fCompleted }};background:#22c55e;" title="{{ $fCompleted }} Completed"></div>@endif
                @if($fInProgress > 0)<div style="flex:{{ $fInProgress }};background:#3b82f6;" title="{{ $fInProgress }} In Progress"></div>@endif
                @if($fPending > 0)<div style="flex:{{ $fPending }};background:#f59e0b;" title="{{ $fPending }} Not Started"></div>@endif
                @if($fExpired > 0)<div style="flex:{{ $fExpired }};background:#ef4444;" title="{{ $fExpired }} Expired"></div>@endif
                @if($fTotal === 1 && $fCompleted === 0 && $fInProgress === 0 && $fPending === 0)
                <div style="flex:1;background:#e2e8f0;"></div>
                @endif
            </div>

            <div style="display:flex;flex-direction:column;gap:12px;">
                @foreach([
                    ['label'=>'Completed','value'=>$fCompleted,'color'=>'#22c55e','bg'=>'#dcfce7'],
                    ['label'=>'In Progress','value'=>$fInProgress,'color'=>'#3b82f6','bg'=>'#dbeafe'],
                    ['label'=>'Not Started','value'=>$fPending,'color'=>'#f59e0b','bg'=>'#fef9c3'],
                    ['label'=>'Expired','value'=>$fExpired,'color'=>'#ef4444','bg'=>'#fee2e2'],
                ] as $row)
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:12px;height:12px;border-radius:3px;background:{{ $row['color'] }};flex-shrink:0;"></div>
                        <span style="font-size:14px;color:#374151;">{{ $row['label'] }}</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:100px;height:6px;background:#f1f5f9;border-radius:3px;overflow:hidden;">
                            <div style="height:100%;width:{{ $fTotal > 0 ? round(($row['value']/$fTotal)*100) : 0 }}%;background:{{ $row['color'] }};border-radius:3px;"></div>
                        </div>
                        <span style="font-size:14px;font-weight:700;color:#1e293b;min-width:24px;text-align:right;">{{ $row['value'] }}</span>
                        <span style="font-size:12px;color:#94a3b8;min-width:36px;text-align:right;">{{ $fTotal > 0 ? round(($row['value']/$fTotal)*100) : 0 }}%</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div style="padding:12px 24px;border-top:1px solid #f1f5f9;text-align:right;">
            <a href="{{ route('analytics.funnels') }}" style="font-size:13px;color:#6366f1;font-weight:600;">View Funnel Analytics →</a>
        </div>
    </div>

    {{-- Form Submission Breakdown --}}
    <div class="card">
        <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;">
            <h3 style="font-size:15px;font-weight:700;color:#1e293b;margin:0;">Form Submission Status</h3>
            <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;">All time breakdown by submission type</p>
        </div>
        <div style="padding:24px;">
            @php
                $sTotal = max($stats['submissions_by_status']['total'], 1);
                $sCompleted = $stats['submissions_by_status']['completed'];
                $sDrafts = $stats['submissions_by_status']['drafts'];
            @endphp

            <div style="display:flex;height:24px;border-radius:12px;overflow:hidden;margin-bottom:20px;">
                @if($sCompleted > 0)<div style="flex:{{ $sCompleted }};background:#22c55e;"></div>@endif
                @if($sDrafts > 0)<div style="flex:{{ $sDrafts }};background:#f59e0b;"></div>@endif
                @if($sCompleted === 0 && $sDrafts === 0)
                <div style="flex:1;background:#e2e8f0;"></div>
                @endif
            </div>

            <div style="display:flex;flex-direction:column;gap:12px;">
                @foreach([
                    ['label'=>'Completed Submissions','value'=>$sCompleted,'color'=>'#22c55e'],
                    ['label'=>'Saved as Draft','value'=>$sDrafts,'color'=>'#f59e0b'],
                ] as $row)
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:12px;height:12px;border-radius:3px;background:{{ $row['color'] }};flex-shrink:0;"></div>
                        <span style="font-size:14px;color:#374151;">{{ $row['label'] }}</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:100px;height:6px;background:#f1f5f9;border-radius:3px;overflow:hidden;">
                            <div style="height:100%;width:{{ round(($row['value']/$sTotal)*100) }}%;background:{{ $row['color'] }};border-radius:3px;"></div>
                        </div>
                        <span style="font-size:14px;font-weight:700;color:#1e293b;min-width:24px;text-align:right;">{{ $row['value'] }}</span>
                        <span style="font-size:12px;color:#94a3b8;min-width:36px;text-align:right;">{{ round(($row['value']/$sTotal)*100) }}%</span>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Top Forms by Submissions --}}
            <div style="margin-top:24px;padding-top:20px;border-top:1px solid #f1f5f9;">
                <div style="font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;">Top Forms by Submissions</div>
                @forelse($stats['top_forms'] as $tf)
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                    <span style="font-size:13px;color:#374151;flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px;">{{ $tf->name }}</span>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="width:80px;height:5px;background:#f1f5f9;border-radius:3px;overflow:hidden;">
                            <div style="height:100%;width:{{ $stats['top_forms'][0]->submissions_count > 0 ? round(($tf->submissions_count/$stats['top_forms'][0]->submissions_count)*100) : 0 }}%;background:#3b82f6;border-radius:3px;"></div>
                        </div>
                        <span style="font-size:13px;font-weight:700;color:#1e293b;min-width:20px;text-align:right;">{{ $tf->submissions_count }}</span>
                    </div>
                </div>
                @empty
                <div style="font-size:13px;color:#94a3b8;text-align:center;padding:12px 0;">No submissions yet</div>
                @endforelse
            </div>
        </div>
        <div style="padding:12px 24px;border-top:1px solid #f1f5f9;text-align:right;">
            <a href="{{ route('analytics.forms') }}" style="font-size:13px;color:#6366f1;font-weight:600;">View Form Analytics →</a>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;">

    {{-- Most Active Patients --}}
    <div class="card">
        <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;">
            <h3 style="font-size:15px;font-weight:700;color:#1e293b;margin:0;">Most Active Patients</h3>
            <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;">Ranked by number of form submissions</p>
        </div>
        <div class="table-responsive">
            <table class="table" style="margin:0;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Patient</th>
                        <th>Submissions</th>
                        <th>Funnels</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stats['top_patients'] as $i => $tp)
                    <tr>
                        <td style="font-weight:700;color:#94a3b8;">{{ $i + 1 }}</td>
                        <td>
                            <div style="font-weight:600;color:#1e293b;">{{ $tp->first_name }} {{ $tp->last_name }}</div>
                            <div style="font-size:12px;color:#94a3b8;">{{ $tp->email }}</div>
                        </td>
                        <td>
                            <span style="font-size:15px;font-weight:800;color:#22c55e;">{{ $tp->submissions_count }}</span>
                        </td>
                        <td>
                            <span style="font-size:15px;font-weight:800;color:#3b82f6;">{{ $tp->assignments_count }}</span>
                        </td>
                        <td>
                            <a href="{{ route('progress.patient', $tp->id) }}" style="font-size:12px;color:#6366f1;font-weight:600;">View →</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:32px;color:#94a3b8;">No patient activity yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Funnels with Lowest Completion (Need Attention) --}}
    <div class="card">
        <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;">
            <h3 style="font-size:15px;font-weight:700;color:#1e293b;margin:0;">Needs Attention</h3>
            <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;">Patients who haven't completed their assigned funnels</p>
        </div>
        <div class="table-responsive">
            <table class="table" style="margin:0;">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Funnel</th>
                        <th>Progress</th>
                        <th>Last Active</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stats['needs_attention'] as $na)
                    <tr>
                        <td>
                            <div style="font-weight:600;color:#1e293b;">{{ $na->patient->first_name }} {{ $na->patient->last_name }}</div>
                        </td>
                        <td style="font-size:13px;color:#374151;">{{ $na->funnel->name }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:6px;">
                                <div style="width:60px;height:5px;background:#e2e8f0;border-radius:3px;overflow:hidden;">
                                    <div style="height:100%;width:{{ $na->progress_percent }}%;background:#f59e0b;border-radius:3px;"></div>
                                </div>
                                <span style="font-size:12px;font-weight:700;color:#374151;">{{ $na->progress_percent }}%</span>
                            </div>
                        </td>
                        <td style="font-size:12px;color:#94a3b8;">{{ $na->last_accessed_at ? $na->last_accessed_at->diffForHumans() : 'Never' }}</td>
                        <td>
                            <a href="{{ route('assignments.show', $na->id) }}" style="font-size:12px;color:#ef4444;font-weight:600;">Follow up →</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:32px;color:#94a3b8;">
                            <div style="font-size:24px;margin-bottom:8px;">🎉</div>
                            All assigned patients have completed their funnels!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:12px 24px;border-top:1px solid #f1f5f9;text-align:right;">
            <a href="{{ route('progress.index') }}" style="font-size:13px;color:#6366f1;font-weight:600;">View All Progress →</a>
        </div>
    </div>
</div>

{{-- Quick Links --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
    <a href="{{ route('analytics.funnels') }}" style="text-decoration:none;">
        <div class="card" style="padding:20px 24px;display:flex;align-items:center;gap:16px;transition:box-shadow .2s;" onmouseover="this.style.boxShadow='0 4px 20px rgba(0,0,0,.1)'" onmouseout="this.style.boxShadow=''">
            <div style="width:48px;height:48px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;flex-shrink:0;">🔀</div>
            <div>
                <div style="font-size:15px;font-weight:700;color:#1e293b;">Funnel Analytics</div>
                <div style="font-size:12px;color:#94a3b8;margin-top:2px;">Per-funnel completion breakdown</div>
            </div>
        </div>
    </a>
    <a href="{{ route('analytics.forms') }}" style="text-decoration:none;">
        <div class="card" style="padding:20px 24px;display:flex;align-items:center;gap:16px;transition:box-shadow .2s;" onmouseover="this.style.boxShadow='0 4px 20px rgba(0,0,0,.1)'" onmouseout="this.style.boxShadow=''">
            <div style="width:48px;height:48px;background:linear-gradient(135deg,#3b82f6,#06b6d4);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;flex-shrink:0;">📝</div>
            <div>
                <div style="font-size:15px;font-weight:700;color:#1e293b;">Form Analytics</div>
                <div style="font-size:12px;color:#94a3b8;margin-top:2px;">Per-form submission stats</div>
            </div>
        </div>
    </a>
    <a href="{{ route('progress.index') }}" style="text-decoration:none;">
        <div class="card" style="padding:20px 24px;display:flex;align-items:center;gap:16px;transition:box-shadow .2s;" onmouseover="this.style.boxShadow='0 4px 20px rgba(0,0,0,.1)'" onmouseout="this.style.boxShadow=''">
            <div style="width:48px;height:48px;background:linear-gradient(135deg,#22c55e,#10b981);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;flex-shrink:0;">📊</div>
            <div>
                <div style="font-size:15px;font-weight:700;color:#1e293b;">Progress Tracking</div>
                <div style="font-size:12px;color:#94a3b8;margin-top:2px;">Per-patient assignment status</div>
            </div>
        </div>
    </a>
</div>

<script>
function setRange(days) {
    const to = new Date();
    const from = new Date();
    from.setDate(from.getDate() - days);
    document.querySelector('input[name="from"]').value = from.toISOString().split('T')[0];
    document.querySelector('input[name="to"]').value = to.toISOString().split('T')[0];
    document.querySelector('form').submit();
}
</script>
@endsection
