@extends('layouts.app')

@section('page-title', 'Users')

@section('content')
<div style="padding: 8px 0 16px;">
    <span style="color:#9ca3af; font-size:13px;">
        <a href="{{ route('dashboard') }}" style="color:#7c3aed; text-decoration:none;">Dashboard</a>
        <span style="margin:0 6px;">›</span>
        <span>Users</span>
    </span>
</div>

<div class="card" style="border-radius:12px; box-shadow:0 1px 6px rgba(0,0,0,0.07); background:#fff; padding:24px;">

    {{-- Top Controls --}}
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
        {{-- Left: entries per page + create --}}
        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            <form method="GET" action="{{ route('user-management.index') }}" id="perPageForm" style="display:flex; align-items:center; gap:8px;">
                <input type="hidden" name="search" value="{{ $search }}">
                <select name="per_page" onchange="document.getElementById('perPageForm').submit()"
                    style="border:1px solid #e5e7eb; border-radius:8px; padding:6px 32px 6px 12px; font-size:14px; color:#374151; background:#fff; cursor:pointer; appearance:auto;">
                    @foreach([10, 25, 50, 100] as $opt)
                        <option value="{{ $opt }}" {{ $perPage == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
                <span style="font-size:14px; color:#6b7280;">Entries Per Page</span>
            </form>

            <a href="#" onclick="openCreateModal()" style="display:inline-flex; align-items:center; gap:6px; background:#7c3aed; color:#fff; padding:8px 18px; border-radius:8px; font-size:14px; font-weight:600; text-decoration:none; border:none; cursor:pointer;">
                <i class="fas fa-plus"></i> Create
            </a>
        </div>

        {{-- Right: search --}}
        <form method="GET" action="{{ route('user-management.index') }}" style="display:flex; align-items:center; gap:8px;">
            <input type="hidden" name="per_page" value="{{ $perPage }}">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search..."
                style="border:1px solid #e5e7eb; border-radius:8px; padding:8px 14px; font-size:14px; width:220px; outline:none; color:#374151;">
        </form>
    </div>

    {{-- Table --}}
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="border-bottom:2px solid #f3f4f6;">
                    <th style="padding:10px 14px; text-align:left; color:#6b7280; font-weight:600; white-space:nowrap;">NO</th>
                    <th style="padding:10px 14px; text-align:left; color:#6b7280; font-weight:600; white-space:nowrap;">NAME <i class="fas fa-sort" style="font-size:11px; opacity:0.5;"></i></th>
                    <th style="padding:10px 14px; text-align:left; color:#6b7280; font-weight:600; white-space:nowrap;">EMAIL <i class="fas fa-sort" style="font-size:11px; opacity:0.5;"></i></th>
                    <th style="padding:10px 14px; text-align:left; color:#6b7280; font-weight:600; white-space:nowrap;">ROLE <i class="fas fa-sort" style="font-size:11px; opacity:0.5;"></i></th>
                    <th style="padding:10px 14px; text-align:left; color:#6b7280; font-weight:600; white-space:nowrap;">STATUS <i class="fas fa-sort" style="font-size:11px; opacity:0.5;"></i></th>
                    <th style="padding:10px 14px; text-align:center; color:#6b7280; font-weight:600;">ACTION</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $user)
                <tr style="border-bottom:1px solid #f3f4f6;" id="user-row-{{ $user->id }}">
                    <td style="padding:14px 14px; color:#374151;">{{ $users->firstItem() + $index }}</td>
                    <td style="padding:14px 14px; color:#111827; font-weight:500;">{{ $user->name }}</td>
                    <td style="padding:14px 14px; color:#374151;">{{ $user->email }}</td>
                    <td style="padding:14px 14px;">
                        @php
                            $roleLabel = ucfirst($user->role ?? 'user');
                            $roleColor = in_array(strtolower($user->role), ['admin','super_admin']) ? '#7c3aed' : '#6b7280';
                        @endphp
                        <span style="background:{{ $roleColor }}; color:#fff; padding:4px 14px; border-radius:20px; font-size:12px; font-weight:600;">
                            {{ $roleLabel }}
                        </span>
                    </td>
                    <td style="padding:14px 14px;">
                        {{-- Toggle switch --}}
                        <label class="toggle-switch" style="cursor:pointer; position:relative; display:inline-block; width:44px; height:24px;">
                            <input type="checkbox" {{ $user->is_active ? 'checked' : '' }}
                                onchange="toggleStatus({{ $user->id }}, this)"
                                style="opacity:0; width:0; height:0; position:absolute;">
                            <span class="toggle-track" data-user="{{ $user->id }}"
                                style="position:absolute; inset:0; border-radius:24px; transition:.3s; background:{{ $user->is_active ? '#7c3aed' : '#d1d5db' }};">
                                <span style="position:absolute; height:18px; width:18px; left:{{ $user->is_active ? '23px' : '3px' }}; bottom:3px; border-radius:50%; background:#fff; transition:.3s; display:block;" class="toggle-knob-{{ $user->id }}"></span>
                            </span>
                        </label>
                    </td>
                    <td style="padding:14px 14px; text-align:center; white-space:nowrap;">
                        {{-- Edit --}}
                        <a href="#" onclick="openEditModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}', '{{ $user->role }}', '{{ $user->phone }}')"
                            style="display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; background:#7c3aed; color:#fff; border-radius:6px; margin-right:6px; text-decoration:none;" title="Edit">
                            <i class="fas fa-edit" style="font-size:13px;"></i>
                        </a>
                        {{-- Delete --}}
                        @if($user->id !== auth()->id())
                        <button onclick="deleteUser({{ $user->id }})"
                            style="display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; background:#ef4444; color:#fff; border-radius:6px; border:none; cursor:pointer;" title="Delete">
                            <i class="fas fa-trash" style="font-size:13px;"></i>
                        </button>
                        @else
                        <button disabled style="display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; background:#fca5a5; color:#fff; border-radius:6px; border:none; cursor:not-allowed;" title="Cannot delete yourself">
                            <i class="fas fa-trash" style="font-size:13px;"></i>
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding:40px; text-align:center; color:#9ca3af;">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
    <div style="margin-top:16px; display:flex; justify-content:flex-end;">
        {{ $users->links() }}
    </div>
    @endif
</div>

{{-- ===== CREATE MODAL ===== --}}
<div id="createModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; width:100%; max-width:480px; padding:28px; box-shadow:0 8px 32px rgba(0,0,0,0.18); position:relative;">
        <h2 style="margin:0 0 20px; font-size:18px; font-weight:700; color:#111827;">Create User</h2>
        <form method="POST" action="{{ route('user-management.store') }}">
            @csrf
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px;">Name</label>
                <input type="text" name="name" required placeholder="Full name"
                    style="width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:9px 12px; font-size:14px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px;">Email</label>
                <input type="email" name="email" required placeholder="email@example.com"
                    style="width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:9px 12px; font-size:14px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px;">Phone</label>
                <input type="text" name="phone" placeholder="Phone number"
                    style="width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:9px 12px; font-size:14px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px;">Role</label>
                <select name="role" style="width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:9px 12px; font-size:14px; box-sizing:border-box;">
                    <option value="admin">Admin</option>
                    <option value="user" selected>User</option>
                    <option value="super_admin">Super Admin</option>
                </select>
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px;">Password</label>
                <input type="password" name="password" required placeholder="Password"
                    style="width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:9px 12px; font-size:14px; box-sizing:border-box;">
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" onclick="closeCreateModal()"
                    style="padding:9px 20px; border:1px solid #e5e7eb; border-radius:8px; background:#fff; color:#374151; font-size:14px; cursor:pointer;">Cancel</button>
                <button type="submit"
                    style="padding:9px 20px; background:#7c3aed; color:#fff; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">Create</button>
            </div>
        </form>
    </div>
</div>

{{-- ===== EDIT MODAL ===== --}}
<div id="editModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; width:100%; max-width:480px; padding:28px; box-shadow:0 8px 32px rgba(0,0,0,0.18); position:relative;">
        <h2 style="margin:0 0 20px; font-size:18px; font-weight:700; color:#111827;">Edit User</h2>
        <form method="POST" id="editForm" action="">
            @csrf
            @method('PUT')
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px;">Name</label>
                <input type="text" name="name" id="editName" required
                    style="width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:9px 12px; font-size:14px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px;">Email</label>
                <input type="email" name="email" id="editEmail" required
                    style="width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:9px 12px; font-size:14px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px;">Phone</label>
                <input type="text" name="phone" id="editPhone"
                    style="width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:9px 12px; font-size:14px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px;">Role</label>
                <select name="role" id="editRole" style="width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:9px 12px; font-size:14px; box-sizing:border-box;">
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                    <option value="super_admin">Super Admin</option>
                </select>
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px;">New Password <span style="color:#9ca3af; font-weight:400;">(leave blank to keep current)</span></label>
                <input type="password" name="password" placeholder="New password (optional)"
                    style="width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:9px 12px; font-size:14px; box-sizing:border-box;">
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" onclick="closeEditModal()"
                    style="padding:9px 20px; border:1px solid #e5e7eb; border-radius:8px; background:#fff; color:#374151; font-size:14px; cursor:pointer;">Cancel</button>
                <button type="submit"
                    style="padding:9px 20px; background:#7c3aed; color:#fff; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
// ── Toggle Status ─────────────────────────────────────────────────────────────
function toggleStatus(userId, checkbox) {
    fetch(`/user-management/${userId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            const track = document.querySelector(`[data-user="${userId}"]`);
            const knob  = document.querySelector(`.toggle-knob-${userId}`);
            if (data.is_active) {
                track.style.background = '#7c3aed';
                knob.style.left = '23px';
            } else {
                track.style.background = '#d1d5db';
                knob.style.left = '3px';
            }
        } else {
            checkbox.checked = !checkbox.checked; // revert
        }
    })
    .catch(() => { checkbox.checked = !checkbox.checked; });
}

// ── Delete User ───────────────────────────────────────────────────────────────
function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) return;
    fetch(`/user-management/${userId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            const row = document.getElementById(`user-row-${userId}`);
            if (row) row.remove();
        } else {
            alert(data.message || 'Could not delete user.');
        }
    });
}

// ── Create Modal ──────────────────────────────────────────────────────────────
function openCreateModal() {
    document.getElementById('createModal').style.display = 'flex';
}
function closeCreateModal() {
    document.getElementById('createModal').style.display = 'none';
}

// ── Edit Modal ────────────────────────────────────────────────────────────────
function openEditModal(id, name, email, role, phone) {
    document.getElementById('editName').value  = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editPhone').value = phone || '';
    document.getElementById('editRole').value  = role;
    document.getElementById('editForm').action = `/user-management/${id}`;
    document.getElementById('editModal').style.display = 'flex';
}
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

// Close modals on backdrop click
document.getElementById('createModal').addEventListener('click', function(e) {
    if (e.target === this) closeCreateModal();
});
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>
@endsection
