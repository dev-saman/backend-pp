@extends('layouts.app')

@section('page-title', 'Users')

@push('styles')
{{-- Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
/* ── Toast ─────────────────────────────────────────────── */
#toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 10px;
    pointer-events: none;
}
.toast {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 20px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    color: #fff;
    min-width: 280px;
    max-width: 380px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    pointer-events: all;
    animation: slideIn .3s ease;
}
.toast.success { background: #16a34a; }
.toast.error   { background: #dc2626; }
@keyframes slideIn {
    from { transform: translateX(120%); opacity: 0; }
    to   { transform: translateX(0);    opacity: 1; }
}
@keyframes slideOut {
    from { transform: translateX(0);    opacity: 1; }
    to   { transform: translateX(120%); opacity: 0; }
}
.toast.hide { animation: slideOut .3s ease forwards; }

/* ── Pagination ─────────────────────────────────────────── */
.pagination-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid #f3f4f6;
}
.pagination-info {
    font-size: 13px;
    color: #6b7280;
}
.pagination {
    display: flex;
    align-items: center;
    gap: 4px;
    list-style: none;
    margin: 0;
    padding: 0;
}
.pagination li a,
.pagination li span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 34px;
    height: 34px;
    padding: 0 10px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    border: 1px solid #e5e7eb;
    color: #374151;
    background: #fff;
    transition: all .2s;
}
.pagination li a:hover {
    background: #7c3aed;
    color: #fff;
    border-color: #7c3aed;
}
.pagination li.active span {
    background: #7c3aed;
    color: #fff;
    border-color: #7c3aed;
}
.pagination li.disabled span {
    color: #d1d5db;
    cursor: not-allowed;
    background: #f9fafb;
}

/* ── Select2 overrides ──────────────────────────────────── */
.select2-container--default .select2-selection--single {
    height: 40px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    display: flex;
    align-items: center;
    padding: 0 12px;
    font-size: 14px;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 40px;
    padding-left: 0;
    color: #374151;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px;
    right: 8px;
}
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #7c3aed;
}
.select2-dropdown {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    font-size: 14px;
}
.select2-search--dropdown .select2-search__field {
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 6px 10px;
    font-size: 13px;
}
</style>
@endpush

@section('content')
{{-- Toast container --}}
<div id="toast-container"></div>

{{-- Hidden flash data for JS toast --}}
@if(session('success'))
<span id="flash-success" data-msg="{{ session('success') }}" style="display:none;"></span>
@endif
@if(session('error'))
<span id="flash-error" data-msg="{{ session('error') }}" style="display:none;"></span>
@endif

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
                            $roleRaw   = strtolower($user->role ?? 'user');
                            $roleLabel = match($roleRaw) {
                                'admin'       => 'Admin',
                                'super_admin' => 'Super Admin',
                                'user'        => 'User',
                                default       => ucwords(str_replace('_', ' ', $roleRaw)),
                            };
                            $roleColor = in_array($roleRaw, ['admin','super_admin']) ? '#7c3aed' : '#6b7280';
                        @endphp
                        <span style="background:{{ $roleColor }}; color:#fff; padding:4px 14px; border-radius:20px; font-size:12px; font-weight:600;">
                            {{ $roleLabel }}
                        </span>
                    </td>
                    <td style="padding:14px 14px;">
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
                        <a href="#" onclick="openEditModal(
                                {{ $user->id }},
                                '{{ addslashes($user->name) }}',
                                '{{ addslashes($user->email) }}',
                                '{{ $user->role }}',
                                '{{ addslashes($user->phone ?? '') }}',
                                '{{ addslashes($user->country_code ?? '') }}',
                                '{{ $user->patient_id ?? '' }}'
                            )"
                            style="display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; background:#7c3aed; color:#fff; border-radius:6px; margin-right:6px; text-decoration:none;" title="Edit">
                            <i class="fas fa-edit" style="font-size:13px;"></i>
                        </a>
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
    <div class="pagination-wrap">
        <div class="pagination-info">
            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
        </div>
        <div>
            {{ $users->onEachSide(2)->links('vendor.pagination.custom') }}
        </div>
    </div>
    @endif
</div>

{{-- ===== CREATE MODAL ===== --}}
<div id="createModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; width:100%; max-width:500px; max-height:90vh; overflow-y:auto; padding:28px; box-shadow:0 8px 32px rgba(0,0,0,0.18); position:relative;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
            <h2 style="margin:0; font-size:18px; font-weight:700; color:#111827;">Create User</h2>
            <button onclick="closeCreateModal()" style="background:none; border:none; cursor:pointer; color:#6b7280; font-size:20px; line-height:1;">×</button>
        </div>
        <form method="POST" action="{{ route('user-management.store') }}">
            @csrf
            {{-- Name --}}
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px;">Name</label>
                <input type="text" name="name" required placeholder="Full name"
                    style="width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:9px 12px; font-size:14px; box-sizing:border-box;">
            </div>
            {{-- Email --}}
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px;">Email</label>
                <input type="email" name="email" required placeholder="email@example.com"
                    style="width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:9px 12px; font-size:14px; box-sizing:border-box;">
            </div>
            {{-- Password --}}
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px;">Password</label>
                <input type="password" name="password" required placeholder="Password"
                    style="width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:9px 12px; font-size:14px; box-sizing:border-box;">
                <p style="font-size:12px; color:#6b7280; margin:6px 0 0;">Password must be at least 8 characters and include:</p>
                <ul style="font-size:12px; color:#6b7280; margin:4px 0 0 18px; padding:0;">
                    <li>One uppercase letter</li><li>One lowercase letter</li>
                    <li>One number</li><li>One special character</li>
                </ul>
            </div>
            {{-- Confirm Password --}}
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px;">Confirm Password</label>
                <input type="password" name="password_confirmation" placeholder="Enter confirm password"
                    style="width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:9px 12px; font-size:14px; box-sizing:border-box;">
            </div>
            {{-- Country Code --}}
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px;">Country Code</label>
                <select name="country_code" id="createCountryCode" style="width:100%;">
                    <option value="">Select country code</option>
                    @foreach($countryCodes as $cc)
                        <option value="{{ $cc['code'] }}">{{ $cc['code'] }} {{ $cc['name'] }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Phone --}}
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px;">Phone Number</label>
                <input type="text" name="phone" placeholder="Enter phone Number"
                    style="width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:9px 12px; font-size:14px; box-sizing:border-box;">
            </div>
            {{-- Role --}}
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px;">Role</label>
                <select name="role" id="createRole" onchange="toggleCreatePatientId(this.value)"
                    style="width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:9px 12px; font-size:14px; box-sizing:border-box;">
                    <option value="admin">Admin</option>
                    <option value="user" selected>User (Patient)</option>
                    <option value="super_admin">Super Admin</option>
                </select>
            </div>
            {{-- Patient ID — shown only when role = user --}}
            <div id="createPatientIdWrap" style="margin-bottom:20px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px;">Patient ID</label>
                <input type="text" name="patient_id" id="createPatientId" placeholder="Enter patient ID"
                    style="width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:9px 12px; font-size:14px; box-sizing:border-box;">
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" onclick="closeCreateModal()"
                    style="padding:9px 24px; border:1px solid #e5e7eb; border-radius:8px; background:#6b7280; color:#fff; font-size:14px; cursor:pointer;">Cancel</button>
                <button type="submit"
                    style="padding:9px 24px; background:#7c3aed; color:#fff; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">Save</button>
            </div>
        </form>
    </div>
</div>

{{-- ===== EDIT MODAL ===== --}}
<div id="editModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; width:100%; max-width:500px; max-height:90vh; overflow-y:auto; padding:28px; box-shadow:0 8px 32px rgba(0,0,0,0.18); position:relative;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
            <h2 style="margin:0; font-size:18px; font-weight:700; color:#111827;">Edit User</h2>
            <button onclick="closeEditModal()" style="background:none; border:none; cursor:pointer; color:#6b7280; font-size:20px; line-height:1;">×</button>
        </div>
        <form method="POST" id="editForm" action="">
            @csrf
            @method('PUT')
            {{-- Name --}}
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px;">Name</label>
                <input type="text" name="name" id="editName" required
                    style="width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:9px 12px; font-size:14px; box-sizing:border-box;">
            </div>
            {{-- Email --}}
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px;">Email</label>
                <input type="email" name="email" id="editEmail" required
                    style="width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:9px 12px; font-size:14px; box-sizing:border-box;">
            </div>
            {{-- Password --}}
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px;">Password</label>
                <input type="password" name="password" id="editPassword" placeholder="Leave blank to keep current"
                    style="width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:9px 12px; font-size:14px; box-sizing:border-box; background:#f0f4ff;">
                <p style="font-size:12px; color:#6b7280; margin:6px 0 0;">Password must be at least 8 characters and include:</p>
                <ul style="font-size:12px; color:#6b7280; margin:4px 0 0 18px; padding:0;">
                    <li>One uppercase letter</li><li>One lowercase letter</li>
                    <li>One number</li><li>One special character</li>
                </ul>
            </div>
            {{-- Confirm Password --}}
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px;">Confirm Password</label>
                <input type="password" name="password_confirmation" id="editPasswordConfirm" placeholder="Enter confirm password"
                    style="width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:9px 12px; font-size:14px; box-sizing:border-box;">
            </div>
            {{-- Country Code --}}
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px;">Country Code</label>
                <select name="country_code" id="editCountryCode" style="width:100%;">
                    <option value="">Select country code</option>
                    @foreach($countryCodes as $cc)
                        <option value="{{ $cc['code'] }}">{{ $cc['code'] }} {{ $cc['name'] }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Phone --}}
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px;">Phone Number</label>
                <input type="text" name="phone" id="editPhone" placeholder="Enter phone Number"
                    style="width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:9px 12px; font-size:14px; box-sizing:border-box;">
            </div>
            {{-- Role --}}
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px;">Role</label>
                <select name="role" id="editRole" onchange="toggleEditPatientId(this.value)"
                    style="width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:9px 12px; font-size:14px; box-sizing:border-box;">
                    <option value="admin">Admin</option>
                    <option value="user">User (Patient)</option>
                    <option value="super_admin">Super Admin</option>
                </select>
            </div>
            {{-- Patient ID --}}
            <div id="editPatientIdWrap" style="margin-bottom:20px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px;">Patient ID</label>
                <input type="text" id="editPatientId" name="patient_id"
                    style="width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:9px 12px; font-size:14px; box-sizing:border-box; background:#f9fafb; color:#6b7280;">
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" onclick="closeEditModal()"
                    style="padding:9px 24px; border:1px solid #e5e7eb; border-radius:8px; background:#6b7280; color:#fff; font-size:14px; cursor:pointer;">Cancel</button>
                <button type="submit"
                    style="padding:9px 24px; background:#7c3aed; color:#fff; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">Save</button>
            </div>
        </form>
    </div>
</div>

{{-- Select2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// ── Toast ─────────────────────────────────────────────────────────────────────
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}`;
    container.appendChild(toast);
    setTimeout(() => {
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

// Show flash messages as toasts on page load
document.addEventListener('DOMContentLoaded', function () {
    const successEl = document.getElementById('flash-success');
    const errorEl   = document.getElementById('flash-error');
    if (successEl) showToast(successEl.dataset.msg, 'success');
    if (errorEl)   showToast(errorEl.dataset.msg,   'error');

    // Init Select2 on country dropdowns
    $('#createCountryCode').select2({
        placeholder: 'Select country code',
        allowClear: true,
        dropdownParent: $('#createModal'),
        width: '100%',
    });
    $('#editCountryCode').select2({
        placeholder: 'Select country code',
        allowClear: true,
        dropdownParent: $('#editModal'),
        width: '100%',
    });

    // Initial state for Patient ID fields
    toggleCreatePatientId(document.getElementById('createRole').value);
    toggleEditPatientId(document.getElementById('editRole').value);
});

// ── Patient ID visibility ─────────────────────────────────────────────────────
function toggleCreatePatientId(role) {
    const wrap = document.getElementById('createPatientIdWrap');
    wrap.style.display = (role === 'user') ? 'block' : 'none';
    document.getElementById('createPatientId').required = (role === 'user');
}
function toggleEditPatientId(role) {
    const wrap = document.getElementById('editPatientIdWrap');
    wrap.style.display = (role === 'user') ? 'block' : 'none';
}

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
            showToast('User status updated.', 'success');
        } else {
            checkbox.checked = !checkbox.checked;
            showToast('Failed to update status.', 'error');
        }
    })
    .catch(() => {
        checkbox.checked = !checkbox.checked;
        showToast('Network error.', 'error');
    });
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
            showToast('User deleted successfully.', 'success');
        } else {
            showToast(data.message || 'Could not delete user.', 'error');
        }
    })
    .catch(() => showToast('Network error.', 'error'));
}

// ── Create Modal ──────────────────────────────────────────────────────────────
function openCreateModal() {
    document.getElementById('createModal').style.display = 'flex';
    toggleCreatePatientId(document.getElementById('createRole').value);
}
function closeCreateModal() {
    document.getElementById('createModal').style.display = 'none';
}

// ── Edit Modal ────────────────────────────────────────────────────────────────
function openEditModal(id, name, email, role, phone, countryCode, patientId) {
    document.getElementById('editName').value  = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editPhone').value = phone || '';

    // Role dropdown
    const roleSelect = document.getElementById('editRole');
    const roleNorm   = (role || '').toLowerCase().trim();
    const roleMap    = { 'admin': 'admin', 'user': 'user', 'super admin': 'super_admin', 'super_admin': 'super_admin' };
    roleSelect.value = roleMap[roleNorm] || roleNorm;
    if (!roleSelect.value) {
        Array.from(roleSelect.options).forEach(opt => {
            if (opt.value === (roleMap[roleNorm] || roleNorm)) opt.selected = true;
        });
    }

    // Patient ID
    document.getElementById('editPatientId').value = patientId || '';
    toggleEditPatientId(roleSelect.value);

    // Password
    document.getElementById('editPassword').value        = '';
    document.getElementById('editPasswordConfirm').value = '';

    // Country code — use Select2 val()
    $('#editCountryCode').val(countryCode || '').trigger('change');

    document.getElementById('editForm').action = `/user-management/${id}`;
    document.getElementById('editModal').style.display = 'flex';
}
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

// ── Password match validation ─────────────────────────────────────────────────
document.getElementById('editForm').addEventListener('submit', function(e) {
    const pw  = document.getElementById('editPassword').value;
    const cpw = document.getElementById('editPasswordConfirm').value;
    if (pw && pw !== cpw) {
        e.preventDefault();
        showToast('Passwords do not match.', 'error');
        return false;
    }
});

// ── Close modals on backdrop click ────────────────────────────────────────────
document.getElementById('createModal').addEventListener('click', function(e) {
    if (e.target === this) closeCreateModal();
});
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>
@endsection
