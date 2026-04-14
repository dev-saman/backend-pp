@extends('layouts.app')

@section('title', 'Assignment Detail')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Assignment Detail</h1>
        <p class="page-subtitle">
            <a href="{{ route('assignments.index') }}" style="color:#3b82f6;">Assignments</a> →
            {{ $assignment->patient->first_name }} {{ $assignment->patient->last_name }} — {{ $assignment->funnel->name }}
        </p>
    </div>
    <div style="display:flex;gap:10px;">
        <button onclick="copyLink('{{ $assignment->fill_url }}')" class="btn btn-secondary">🔗 Copy Patient Link</button>
        <button onclick="resendLink({{ $assignment->id }})" class="btn btn-secondary">🔄 Regenerate Link</button>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:24px;">

    {{-- Left: Steps --}}
    <div>
        <div class="card" style="margin-bottom:20px;">
            <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;">
                <h3 style="font-size:15px;font-weight:700;color:#1e293b;">Form Progress</h3>
            </div>
            <div style="padding:20px 24px;">
                {{-- Overall progress bar --}}
                <div style="margin-bottom:24px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                        <span style="font-size:13px;font-weight:600;color:#374151;">Overall Completion</span>
                        <span style="font-size:13px;font-weight:700;color:{{ $assignment->progress_percent==100 ? '#22c55e' : '#3b82f6' }};">
                            {{ $assignment->progress_percent }}%
                        </span>
                    </div>
                    <div style="height:10px;background:#e2e8f0;border-radius:5px;overflow:hidden;">
                        <div style="height:100%;width:{{ $assignment->progress_percent }}%;background:{{ $assignment->progress_percent==100 ? '#22c55e' : 'linear-gradient(90deg,#3b82f6,#8b5cf6)' }};border-radius:5px;transition:width .5s;"></div>
                    </div>
                    <div style="font-size:12px;color:#94a3b8;margin-top:4px;">
                        {{ $assignment->forms_completed }} of {{ $assignment->forms_total }} forms completed
                    </div>
                </div>

                {{-- Steps --}}
                @foreach($steps as $step)
                @php
                    $statusIcon = match($step['status']) {
                        'completed' => '✅',
                        'draft'     => '💾',
                        default     => '⏳',
                    };
                    $statusColor = match($step['status']) {
                        'completed' => '#22c55e',
                        'draft'     => '#f59e0b',
                        default     => '#94a3b8',
                    };
                    $statusLabel = match($step['status']) {
                        'completed' => 'Completed',
                        'draft'     => 'In Progress (Draft)',
                        default     => 'Not Started',
                    };
                @endphp
                <div style="display:flex;gap:16px;padding:16px 0;border-bottom:1px solid #f8fafc;align-items:flex-start;">
                    <div style="width:36px;height:36px;border-radius:50%;background:{{ $statusColor }}20;color:{{ $statusColor }};display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;">
                        {{ $statusIcon }}
                    </div>
                    <div style="flex:1;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                            <div style="font-weight:600;color:#1e293b;font-size:14px;">
                                Step {{ $step['step'] }}: {{ $step['form_name'] }}
                            </div>
                            <span style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:12px;background:{{ $statusColor }}20;color:{{ $statusColor }};">
                                {{ $statusLabel }}
                            </span>
                        </div>
                        @if($step['submitted'])
                        <div style="font-size:12px;color:#64748b;">
                            Submitted: {{ $step['submitted']->format('M j, Y g:i A') }}
                        </div>
                        @elseif($step['last_saved'])
                        <div style="font-size:12px;color:#f59e0b;">
                            Last saved: {{ $step['last_saved']->format('M j, Y g:i A') }}
                        </div>
                        @endif

                        {{-- Show submitted data preview --}}
                        @if(!empty($step['data']) && $step['status'] === 'completed')
                        <details style="margin-top:8px;">
                            <summary style="font-size:12px;color:#3b82f6;cursor:pointer;font-weight:500;">View submitted data</summary>
                            <div style="margin-top:8px;background:#f8fafc;border-radius:6px;padding:12px;font-size:12px;color:#374151;">
                                @foreach($step['data'] as $key => $val)
                                <div style="margin-bottom:4px;">
                                    <strong>{{ $key }}:</strong>
                                    {{ is_array($val) ? implode(', ', $val) : (strlen($val) > 100 ? substr($val, 0, 100).'…' : $val) }}
                                </div>
                                @endforeach
                            </div>
                        </details>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Right: Info panel --}}
    <div>
        <div class="card" style="margin-bottom:16px;">
            <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;">
                <h3 style="font-size:15px;font-weight:700;color:#1e293b;">Patient</h3>
            </div>
            <div style="padding:20px 24px;">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                    <div style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#8b5cf6);color:#fff;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;">
                        {{ strtoupper(substr($assignment->patient->first_name, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight:700;color:#1e293b;">{{ $assignment->patient->first_name }} {{ $assignment->patient->last_name }}</div>
                        <div style="font-size:12px;color:#64748b;">{{ $assignment->patient->email }}</div>
                    </div>
                </div>
                <a href="{{ route('patients.show', $assignment->patient) }}" class="btn btn-secondary" style="width:100%;text-align:center;">View Patient Profile</a>
            </div>
        </div>

        <div class="card" style="margin-bottom:16px;">
            <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;">
                <h3 style="font-size:15px;font-weight:700;color:#1e293b;">Assignment Info</h3>
            </div>
            <div style="padding:20px 24px;font-size:13px;">
                @php
                    $statusColors = ['pending'=>'#f59e0b','in_progress'=>'#3b82f6','completed'=>'#22c55e','expired'=>'#ef4444'];
                @endphp
                <div style="display:flex;justify-content:space-between;margin-bottom:12px;">
                    <span style="color:#64748b;">Status</span>
                    <span style="font-weight:600;color:{{ $statusColors[$assignment->status] ?? '#94a3b8' }};">
                        {{ ucfirst(str_replace('_',' ',$assignment->status)) }}
                    </span>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:12px;">
                    <span style="color:#64748b;">Assigned By</span>
                    <span style="font-weight:500;color:#374151;">{{ $assignment->assignedBy->name ?? '—' }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:12px;">
                    <span style="color:#64748b;">Assigned On</span>
                    <span style="color:#374151;">{{ $assignment->created_at->format('M j, Y') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:12px;">
                    <span style="color:#64748b;">Last Accessed</span>
                    <span style="color:#374151;">{{ $assignment->last_accessed_at ? $assignment->last_accessed_at->diffForHumans() : 'Never' }}</span>
                </div>
                @if($assignment->completed_at)
                <div style="display:flex;justify-content:space-between;margin-bottom:12px;">
                    <span style="color:#64748b;">Completed</span>
                    <span style="color:#22c55e;font-weight:600;">{{ $assignment->completed_at->format('M j, Y') }}</span>
                </div>
                @endif
                @if($assignment->expires_at)
                <div style="display:flex;justify-content:space-between;margin-bottom:12px;">
                    <span style="color:#64748b;">Expires</span>
                    <span style="color:{{ $assignment->is_expired ? '#ef4444' : '#374151' }};">
                        {{ $assignment->expires_at->format('M j, Y') }}
                        {{ $assignment->is_expired ? '(Expired)' : '' }}
                    </span>
                </div>
                @endif
                @if($assignment->note)
                <div style="margin-top:12px;padding:10px;background:#f8fafc;border-radius:6px;color:#374151;">
                    <div style="font-size:11px;font-weight:600;color:#94a3b8;margin-bottom:4px;">NOTE</div>
                    {{ $assignment->note }}
                </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;">
                <h3 style="font-size:15px;font-weight:700;color:#1e293b;">Patient Link</h3>
            </div>
            <div style="padding:20px 24px;">
                <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px;font-size:12px;color:#374151;word-break:break-all;margin-bottom:12px;">
                    {{ $assignment->fill_url }}
                </div>
                <button onclick="copyLink('{{ $assignment->fill_url }}')" class="btn btn-primary" style="width:100%;margin-bottom:8px;">
                    📋 Copy Link
                </button>
                <a href="{{ $assignment->fill_url }}" target="_blank" class="btn btn-secondary" style="width:100%;text-align:center;">
                    ↗ Open as Patient
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function copyLink(url) {
    navigator.clipboard.writeText(url).then(() => {
        const el = document.createElement('div');
        el.textContent = '✓ Link copied to clipboard!';
        el.style.cssText = 'position:fixed;bottom:20px;left:50%;transform:translateX(-50%);background:#1e293b;color:#fff;padding:10px 20px;border-radius:8px;font-size:13px;z-index:9999;';
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 2000);
    });
}

function resendLink(id) {
    if (!confirm('Regenerate a new link? The old link will stop working.')) return;
    fetch(`/assignments/${id}/resend`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            copyLink(res.fill_url);
            alert('New link generated and copied!');
            location.reload();
        }
    });
}
</script>
@endsection
