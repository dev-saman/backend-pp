@extends('layouts.app')

@section('title', 'Form Analytics')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Form Analytics</h1>
        <p class="page-subtitle">Submission statistics for every form — track who submitted, who saved a draft, and who hasn't started</p>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('analytics.funnels') }}" class="btn btn-secondary">Funnel Analytics</a>
        <a href="{{ route('analytics.reports') }}" class="btn btn-primary">Reports Overview</a>
    </div>
</div>

{{-- Top Summary Stats --}}
<div class="stats-grid" style="grid-template-columns:repeat(5,1fr);margin-bottom:28px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff;color:#3b82f6;">📝</div>
        <div class="stat-info">
            <div class="stat-value">{{ $summary['total_forms'] }}</div>
            <div class="stat-label">Total Forms</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f0fdf4;color:#22c55e;">✅</div>
        <div class="stat-info">
            <div class="stat-value">{{ $summary['total_submissions'] }}</div>
            <div class="stat-label">Total Submissions</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef9c3;color:#ca8a04;">💾</div>
        <div class="stat-info">
            <div class="stat-value">{{ $summary['total_drafts'] }}</div>
            <div class="stat-label">Saved as Draft</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f5f3ff;color:#7c3aed;">📊</div>
        <div class="stat-info">
            <div class="stat-value">{{ $summary['avg_completion_rate'] }}%</div>
            <div class="stat-label">Avg Completion Rate</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fff7ed;color:#ea580c;">🔄</div>
        <div class="stat-info">
            <div class="stat-value">{{ $summary['active_forms'] }}</div>
            <div class="stat-label">Active Forms</div>
        </div>
    </div>
</div>

{{-- Per-Form Cards --}}
@forelse($forms as $form)
<div class="card" style="margin-bottom:24px;">
    {{-- Form Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #f1f5f9;">
        <div style="display:flex;align-items:center;gap:14px;">
            <div style="width:44px;height:44px;background:linear-gradient(135deg,#3b82f6,#06b6d4);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;">📝</div>
            <div>
                <div style="font-size:17px;font-weight:700;color:#1e293b;">{{ $form->name }}</div>
                <div style="font-size:12px;color:#94a3b8;margin-top:2px;">
                    {{ $form->field_count }} field{{ $form->field_count != 1 ? 's' : '' }}
                    &nbsp;·&nbsp;
                    <span style="color:{{ $form->status === 'active' ? '#22c55e' : '#f59e0b' }};">{{ ucfirst($form->status) }}</span>
                    @if($form->category)
                    &nbsp;·&nbsp; {{ ucfirst($form->category) }}
                    @endif
                    &nbsp;·&nbsp; Created {{ $form->created_at->format('M d, Y') }}
                </div>
            </div>
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            <a href="{{ route('forms.show', $form->id) }}" class="btn btn-sm btn-secondary">View Submissions</a>
            <a href="{{ route('forms.builder', $form->id) }}" class="btn btn-sm btn-secondary">Edit Form</a>
        </div>
    </div>

    {{-- Stats Row --}}
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:0;border-bottom:1px solid #f1f5f9;">
        @php
            $total = $form->stats['total_submissions'];
            $completed = $form->stats['completed'];
            $drafts = $form->stats['drafts'];
            $rate = $total > 0 ? round(($completed / $total) * 100) : 0;
            $unique = $form->stats['unique_patients'];
        @endphp
        <div style="padding:16px 20px;border-right:1px solid #f1f5f9;text-align:center;">
            <div style="font-size:24px;font-weight:800;color:#1e293b;">{{ $total }}</div>
            <div style="font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-top:2px;">Total Submissions</div>
        </div>
        <div style="padding:16px 20px;border-right:1px solid #f1f5f9;text-align:center;">
            <div style="font-size:24px;font-weight:800;color:#22c55e;">{{ $completed }}</div>
            <div style="font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-top:2px;">Completed</div>
        </div>
        <div style="padding:16px 20px;border-right:1px solid #f1f5f9;text-align:center;">
            <div style="font-size:24px;font-weight:800;color:#f59e0b;">{{ $drafts }}</div>
            <div style="font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-top:2px;">Saved as Draft</div>
        </div>
        <div style="padding:16px 20px;border-right:1px solid #f1f5f9;text-align:center;">
            <div style="font-size:24px;font-weight:800;color:#6366f1;">{{ $unique }}</div>
            <div style="font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-top:2px;">Unique Patients</div>
        </div>
        <div style="padding:16px 20px;text-align:center;">
            <div style="font-size:24px;font-weight:800;color:{{ $rate >= 75 ? '#22c55e' : ($rate >= 40 ? '#f59e0b' : '#ef4444') }};">{{ $rate }}%</div>
            <div style="font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-top:2px;">Completion Rate</div>
        </div>
    </div>

    {{-- Completion Rate Bar --}}
    @if($total > 0)
    <div style="padding:16px 24px;border-bottom:1px solid #f1f5f9;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
            <span style="font-size:12px;font-weight:600;color:#64748b;">Submission Breakdown</span>
            <span style="font-size:12px;color:#94a3b8;">{{ $total }} total submissions</span>
        </div>
        <div style="height:10px;background:#f1f5f9;border-radius:5px;overflow:hidden;display:flex;">
            @if($completed > 0)
            <div style="width:{{ ($completed/$total)*100 }}%;background:#22c55e;height:100%;" title="{{ $completed }} Completed"></div>
            @endif
            @if($drafts > 0)
            <div style="width:{{ ($drafts/$total)*100 }}%;background:#f59e0b;height:100%;" title="{{ $drafts }} Drafts"></div>
            @endif
        </div>
        <div style="display:flex;gap:16px;margin-top:8px;">
            <span style="font-size:11px;color:#22c55e;display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;background:#22c55e;border-radius:50%;display:inline-block;"></span>Completed ({{ $completed }})</span>
            <span style="font-size:11px;color:#f59e0b;display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;background:#f59e0b;border-radius:50%;display:inline-block;"></span>Draft / Partial ({{ $drafts }})</span>
        </div>
    </div>

    {{-- Recent Submissions Table --}}
    <div>
        <div style="padding:12px 24px;background:#f8fafc;border-bottom:1px solid #f1f5f9;">
            <span style="font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.5px;">Recent Submissions</span>
        </div>
        <div class="table-responsive">
            <table class="table" style="margin:0;">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Submitted Via</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($form->recentSubmissions as $sub)
                    @php
                        $ss = ['completed'=>['bg'=>'#dcfce7','color'=>'#16a34a','label'=>'Completed'],
                               'draft'=>['bg'=>'#fef9c3','color'=>'#ca8a04','label'=>'Draft'],
                               'partial'=>['bg'=>'#fff7ed','color'=>'#ea580c','label'=>'Partial']];
                        $sv = $ss[$sub->status ?? 'completed'] ?? ['bg'=>'#dcfce7','color'=>'#16a34a','label'=>'Completed'];
                    @endphp
                    <tr>
                        <td>
                            @if($sub->patient)
                            <div style="font-weight:600;color:#1e293b;">{{ $sub->patient->first_name }} {{ $sub->patient->last_name }}</div>
                            <div style="font-size:12px;color:#94a3b8;">{{ $sub->patient->email }}</div>
                            @elseif($sub->patient_name)
                            <div style="font-weight:600;color:#1e293b;">{{ $sub->patient_name }}</div>
                            <div style="font-size:12px;color:#94a3b8;">{{ $sub->patient_email ?? 'Public submission' }}</div>
                            @else
                            <div style="font-weight:500;color:#94a3b8;font-style:italic;">Anonymous</div>
                            @endif
                        </td>
                        <td>
                            <span style="font-size:12px;color:#64748b;">
                                {{ $sub->assignment_id ? '🔀 Via Funnel' : '🔗 Direct Link' }}
                            </span>
                        </td>
                        <td>
                            <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $sv['bg'] }};color:{{ $sv['color'] }};">
                                {{ $sv['label'] }}
                            </span>
                        </td>
                        <td style="font-size:12px;color:#94a3b8;">{{ $sub->created_at->format('M d, Y g:i A') }}</td>
                        <td>
                            <a href="{{ route('forms.show', $form->id) }}" style="font-size:12px;color:#6366f1;font-weight:600;">View →</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:24px;color:#94a3b8;">No submissions yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($form->stats['total_submissions'] > 5)
        <div style="padding:12px 24px;text-align:center;border-top:1px solid #f1f5f9;">
            <a href="{{ route('forms.show', $form->id) }}" style="font-size:13px;color:#6366f1;font-weight:600;">
                View all {{ $form->stats['total_submissions'] }} submissions →
            </a>
        </div>
        @endif
    </div>
    @else
    <div style="padding:32px;text-align:center;color:#94a3b8;">
        <div style="font-size:32px;margin-bottom:8px;">📭</div>
        <div style="font-weight:600;margin-bottom:4px;">No submissions yet</div>
        <div style="font-size:13px;">Share this form's public link or add it to a funnel to start collecting submissions.</div>
    </div>
    @endif
</div>
@empty
<div class="card" style="padding:64px;text-align:center;">
    <div style="font-size:48px;margin-bottom:16px;">📝</div>
    <div style="font-size:18px;font-weight:700;color:#1e293b;margin-bottom:8px;">No forms created yet</div>
    <p style="color:#94a3b8;margin-bottom:20px;">Create your first form to start collecting patient data.</p>
    <a href="{{ route('forms.create') }}" class="btn btn-primary">Create Form</a>
</div>
@endforelse
@endsection
