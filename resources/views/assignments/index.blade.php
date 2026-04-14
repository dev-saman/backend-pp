@extends('layouts.app')

@section('title', 'Funnel Assignments')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Funnel Assignments</h1>
        <p class="page-subtitle">Assign funnels to patients and track their progress</p>
    </div>
    <button class="btn btn-primary" onclick="document.getElementById('assignModal').style.display='flex'">
        + Assign Funnel
    </button>
</div>

{{-- Stats --}}
<div class="stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff;color:#3b82f6;">📋</div>
        <div class="stat-info">
            <div class="stat-value">{{ $assignments->total() }}</div>
            <div class="stat-label">Total Assignments</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef9c3;color:#ca8a04;">⏳</div>
        <div class="stat-info">
            <div class="stat-value">{{ $assignments->where('status','pending')->count() }}</div>
            <div class="stat-label">Pending</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fff7ed;color:#ea580c;">🔄</div>
        <div class="stat-info">
            <div class="stat-value">{{ $assignments->where('status','in_progress')->count() }}</div>
            <div class="stat-label">In Progress</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f0fdf4;color:#22c55e;">✅</div>
        <div class="stat-info">
            <div class="stat-value">{{ $assignments->where('status','completed')->count() }}</div>
            <div class="stat-label">Completed</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card" style="margin-bottom:20px;">
    <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
        <div style="flex:1;min-width:180px;">
            <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">Patient</label>
            <select name="patient_id" class="form-control" style="height:38px;">
                <option value="">All Patients</option>
                @foreach($patients as $p)
                <option value="{{ $p->id }}" {{ request('patient_id') == $p->id ? 'selected' : '' }}>
                    {{ $p->first_name }} {{ $p->last_name }}
                </option>
                @endforeach
            </select>
        </div>
        <div style="flex:1;min-width:180px;">
            <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">Funnel</label>
            <select name="funnel_id" class="form-control" style="height:38px;">
                <option value="">All Funnels</option>
                @foreach($funnels as $f)
                <option value="{{ $f->id }}" {{ request('funnel_id') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="min-width:140px;">
            <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">Status</label>
            <select name="status" class="form-control" style="height:38px;">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status')==='pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ request('status')==='in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ request('status')==='completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary" style="height:38px;">Filter</button>
            <a href="{{ route('assignments.index') }}" class="btn btn-secondary" style="height:38px;line-height:38px;padding:0 16px;">Clear</a>
        </div>
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
                    <th>Assigned By</th>
                    <th>Last Accessed</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignments as $a)
                <tr>
                    <td>
                        <div style="font-weight:600;color:#1e293b;">
                            {{ $a->patient->first_name }} {{ $a->patient->last_name }}
                        </div>
                        <div style="font-size:12px;color:#94a3b8;">{{ $a->patient->email }}</div>
                    </td>
                    <td>
                        <div style="font-weight:500;color:#374151;">{{ $a->funnel->name }}</div>
                        <div style="font-size:12px;color:#94a3b8;">{{ $a->forms_total }} forms</div>
                    </td>
                    <td style="min-width:160px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="flex:1;height:6px;background:#e2e8f0;border-radius:3px;overflow:hidden;">
                                <div style="height:100%;width:{{ $a->progress_percent }}%;background:{{ $a->progress_percent==100 ? '#22c55e' : '#3b82f6' }};border-radius:3px;"></div>
                            </div>
                            <span style="font-size:12px;font-weight:600;color:#64748b;white-space:nowrap;">
                                {{ $a->forms_completed }}/{{ $a->forms_total }}
                            </span>
                        </div>
                        <div style="font-size:11px;color:#94a3b8;margin-top:2px;">{{ $a->progress_percent }}% complete</div>
                    </td>
                    <td>
                        @php
                            $statusColors = ['pending'=>'#f59e0b','in_progress'=>'#3b82f6','completed'=>'#22c55e','expired'=>'#ef4444'];
                            $statusLabels = ['pending'=>'Pending','in_progress'=>'In Progress','completed'=>'Completed','expired'=>'Expired'];
                        @endphp
                        <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;background:{{ $statusColors[$a->status] ?? '#94a3b8' }}20;color:{{ $statusColors[$a->status] ?? '#94a3b8' }};">
                            {{ $statusLabels[$a->status] ?? ucfirst($a->status) }}
                        </span>
                    </td>
                    <td style="font-size:13px;color:#64748b;">{{ $a->assignedBy->name ?? '—' }}</td>
                    <td style="font-size:13px;color:#64748b;">
                        {{ $a->last_accessed_at ? $a->last_accessed_at->diffForHumans() : 'Never' }}
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('assignments.show', $a) }}" class="btn btn-sm btn-secondary" title="View Progress">👁</a>
                            <button class="btn btn-sm" style="background:#eff6ff;color:#3b82f6;border:1px solid #bfdbfe;"
                                    onclick="copyLink('{{ $a->fill_url }}')" title="Copy Patient Link">🔗</button>
                            <button class="btn btn-sm" style="background:#f0fdf4;color:#22c55e;border:1px solid #bbf7d0;"
                                    onclick="resendLink({{ $a->id }})" title="Regenerate Link">🔄</button>
                            <button class="btn btn-sm" style="background:#fef2f2;color:#ef4444;border:1px solid #fecaca;"
                                    onclick="deleteAssignment({{ $a->id }})" title="Remove">✕</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:48px;color:#94a3b8;">
                        No assignments yet. Click "Assign Funnel" to get started.
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

{{-- Assign Funnel Modal --}}
<div id="assignModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:32px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.2);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
            <h2 style="font-size:18px;font-weight:700;color:#1e293b;">Assign Funnel to Patient</h2>
            <button onclick="document.getElementById('assignModal').style.display='none'"
                    style="background:none;border:none;font-size:20px;cursor:pointer;color:#94a3b8;">✕</button>
        </div>
        <form id="assignForm">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Patient *</label>
                <select name="patient_id" id="assignPatient" class="form-control" required style="height:42px;">
                    <option value="">Select a patient…</option>
                    @foreach($patients as $p)
                    <option value="{{ $p->id }}">{{ $p->first_name }} {{ $p->last_name }} — {{ $p->email }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom:16px;">
                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Funnel *</label>
                <select name="funnel_id" id="assignFunnel" class="form-control" required style="height:42px;">
                    <option value="">Select a funnel…</option>
                    @foreach($funnels as $f)
                    <option value="{{ $f->id }}">{{ $f->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom:16px;">
                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Note (optional)</label>
                <textarea name="note" class="form-control" rows="2" placeholder="Add a note for this assignment…" style="resize:none;"></textarea>
            </div>
            <div style="margin-bottom:24px;">
                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Expires At (optional)</label>
                <input type="datetime-local" name="expires_at" class="form-control" style="height:42px;">
            </div>

            {{-- Result area --}}
            <div id="assignResult" style="display:none;background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:16px;margin-bottom:16px;">
                <div style="font-size:13px;font-weight:600;color:#166534;margin-bottom:8px;">✅ Assigned successfully!</div>
                <div style="font-size:12px;color:#374151;margin-bottom:8px;">Patient link:</div>
                <div style="display:flex;gap:8px;">
                    <input type="text" id="assignedLink" readonly style="flex:1;font-size:12px;padding:8px;border:1px solid #d1fae5;border-radius:6px;background:#fff;">
                    <button type="button" onclick="copyLink(document.getElementById('assignedLink').value)"
                            style="padding:8px 12px;background:#22c55e;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:12px;">Copy</button>
                </div>
            </div>

            <div style="display:flex;gap:12px;">
                <button type="button" onclick="document.getElementById('assignModal').style.display='none'"
                        class="btn btn-secondary" style="flex:1;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex:1;" id="assignSubmitBtn">Assign & Generate Link</button>
            </div>
        </form>
    </div>
</div>

<script>
// Assign form submit
document.getElementById('assignForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('assignSubmitBtn');
    btn.disabled = true;
    btn.textContent = 'Assigning…';

    const formData = new FormData(this);

    fetch('/assignments', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(Object.fromEntries(formData)),
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.textContent = 'Assign & Generate Link';

        if (res.status === 'success') {
            document.getElementById('assignedLink').value = res.assignment.fill_url;
            document.getElementById('assignResult').style.display = 'block';
            setTimeout(() => location.reload(), 3000);
        } else {
            alert(res.message || 'Error assigning funnel.');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.textContent = 'Assign & Generate Link';
        alert('Error. Please try again.');
    });
});

function copyLink(url) {
    navigator.clipboard.writeText(url).then(() => {
        const el = document.createElement('div');
        el.textContent = '✓ Link copied!';
        el.style.cssText = 'position:fixed;bottom:20px;left:50%;transform:translateX(-50%);background:#1e293b;color:#fff;padding:10px 20px;border-radius:8px;font-size:13px;z-index:9999;';
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 2000);
    });
}

function resendLink(id) {
    if (!confirm('Regenerate a new link for this patient? The old link will stop working.')) return;
    fetch(`/assignments/${id}/resend`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            copyLink(res.fill_url);
            alert('New link generated and copied to clipboard!');
        }
    });
}

function deleteAssignment(id) {
    if (!confirm('Remove this assignment? The patient link will stop working.')) return;
    fetch(`/assignments/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') location.reload();
    });
}
</script>
@endsection
