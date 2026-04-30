@extends('layouts.app')
@section('title', 'Create Form - AdvantageHCS Admin')
@section('page-title', 'Create Form')
@section('page-subtitle', 'Set up a new patient form')
@section('header-actions')
    <a href="{{ route('forms.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
@endsection

@push('styles')
<style>
    .assign-toggle-row {
        display: flex !important;
        align-items: center !important;
        gap: 40px !important;
        margin-bottom: 16px !important;
        flex-wrap: nowrap !important;
    }
    .assign-toggle-item {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }
    .assign-toggle-item .toggle-label {
        font-size: 14px !important;
        font-weight: 500 !important;
        color: #374151 !important;
        white-space: nowrap !important;
    }
    /* Toggle switch */
    label.assign-toggle-switch {
        position: relative !important;
        display: inline-block !important;
        width: 44px !important;
        height: 24px !important;
        cursor: pointer !important;
        margin: 0 !important;
        padding: 0 !important;
        vertical-align: middle !important;
    }
    label.assign-toggle-switch input[type=checkbox] {
        opacity: 0 !important;
        width: 0 !important;
        height: 0 !important;
        position: absolute !important;
        margin: 0 !important;
    }
    label.assign-toggle-switch .assign-toggle-track {
        position: absolute !important;
        top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important;
        border-radius: 24px !important;
        transition: background .3s !important;
        display: block !important;
    }
    label.assign-toggle-switch .assign-toggle-knob {
        position: absolute !important;
        height: 18px !important;
        width: 18px !important;
        bottom: 3px !important;
        border-radius: 50% !important;
        background: #fff !important;
        transition: left .3s !important;
        display: block !important;
    }
    .section-heading {
        font-size: 14px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 14px;
        display: block;
    }
</style>
@endpush

@section('content')
<div class="card" style="max-width:700px;">
    <div class="card-header">
        <span class="card-title">Form Details</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('forms.store') }}">
            @csrf

            {{-- Form Name --}}
            <div class="form-group">
                <label class="form-label">Form Name <span style="color:#ef4444;">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                       placeholder="e.g. Patient Intake Form" required>
            </div>

            {{-- Description --}}
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"
                          placeholder="Describe what this form is for...">{{ old('description') }}</textarea>
            </div>

            {{-- Success Message --}}
            <div class="form-group">
                <label class="form-label">Success Message</label>
                <textarea name="success_msg" class="form-control" rows="3"
                          placeholder="Message shown after successful form submission...">{{ old('success_msg') }}</textarea>
            </div>

            {{-- Thanks Message --}}
            <div class="form-group">
                <label class="form-label">Thanks Message</label>
                <textarea name="thanks_msg" class="form-control" rows="3"
                          placeholder="Message shown on the thank-you page...">{{ old('thanks_msg') }}</textarea>
            </div>

            {{-- Assign Form --}}
            <div class="form-group">
                <label class="form-label section-heading">Assign Form</label>
                <div class="assign-toggle-row">
                    {{-- Role toggle --}}
                    <div class="assign-toggle-item">
                        <span class="toggle-label">Role</span>
                        <label class="assign-toggle-switch">
                            <input type="checkbox" name="assign_role_enabled" id="assign_role_toggle"
                                   value="1" {{ old('assign_role_enabled') ? 'checked' : '' }}
                                   onchange="assignToggleChanged('role', this)">
                            <span class="assign-toggle-track" id="role_track"
                                style="background:{{ old('assign_role_enabled') ? '#C8102E' : '#d1d5db' }};">
                                <span class="assign-toggle-knob" id="role_knob"
                                    style="left:{{ old('assign_role_enabled') ? '23px' : '3px' }};"></span>
                            </span>
                        </label>
                    </div>
                    {{-- User toggle --}}
                    <div class="assign-toggle-item">
                        <span class="toggle-label">User</span>
                        <label class="assign-toggle-switch">
                            <input type="checkbox" name="assign_user_enabled" id="assign_user_toggle"
                                   value="1" {{ old('assign_user_enabled') ? 'checked' : '' }}
                                   onchange="assignToggleChanged('user', this)">
                            <span class="assign-toggle-track" id="user_track"
                                style="background:{{ old('assign_user_enabled') ? '#C8102E' : '#d1d5db' }};">
                                <span class="assign-toggle-knob" id="user_knob"
                                    style="left:{{ old('assign_user_enabled') ? '23px' : '3px' }};"></span>
                            </span>
                        </label>
                    </div>
                    {{-- Public toggle --}}
                    <div class="assign-toggle-item">
                        <span class="toggle-label">Public</span>
                        <label class="assign-toggle-switch">
                            <input type="checkbox" name="assign_public_enabled" id="assign_public_toggle"
                                   value="1" {{ old('assign_public_enabled') ? 'checked' : '' }}
                                   onchange="assignToggleChanged('public', this)">
                            <span class="assign-toggle-track" id="public_track"
                                style="background:{{ old('assign_public_enabled') ? '#C8102E' : '#d1d5db' }};">
                                <span class="assign-toggle-knob" id="public_knob"
                                    style="left:{{ old('assign_public_enabled') ? '23px' : '3px' }};"></span>
                            </span>
                        </label>
                    </div>
                </div>

                {{-- Role dropdown --}}
                <div id="role_field" style="{{ old('assign_role_enabled') ? '' : 'display:none;' }}">
                    <div class="form-group" style="margin-top:10px;">
                        <label class="form-label">Role</label>
                        <select name="assign_type" class="form-control">
                            <option value="">Select Role</option>
                            <option value="admin"       {{ old('assign_type') === 'admin'       ? 'selected' : '' }}>Admin</option>
                            <option value="super_admin" {{ old('assign_type') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                            <option value="user"        {{ old('assign_type') === 'user'        ? 'selected' : '' }}>User</option>
                        </select>
                    </div>
                </div>

                {{-- User dropdown --}}
                <div id="user_field" style="{{ old('assign_user_enabled') ? '' : 'display:none;' }}">
                    <div class="form-group" style="margin-top:10px;">
                        <label class="form-label">Select User</label>
                        <select name="assign_user_id" class="form-control">
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assign_user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Category & Status --}}
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control">
                        <option value="">Select Category</option>
                        <option value="intake"         {{ old('category') === 'intake'         ? 'selected' : '' }}>Patient Intake</option>
                        <option value="consent"        {{ old('category') === 'consent'        ? 'selected' : '' }}>Consent Form</option>
                        <option value="follow-up"      {{ old('category') === 'follow-up'      ? 'selected' : '' }}>Follow-up</option>
                        <option value="health-history" {{ old('category') === 'health-history' ? 'selected' : '' }}>Health History</option>
                        <option value="hipaa"          {{ old('category') === 'hipaa'          ? 'selected' : '' }}>HIPAA</option>
                        <option value="other"          {{ old('category') === 'other'          ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="draft"  {{ old('status', 'draft') === 'draft'  ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                    </select>
                </div>
            </div>

            {{-- Submit buttons --}}
            <div style="display:flex; gap:12px; margin-top:8px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Create Form</button>
                <a href="{{ route('forms.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function assignToggleChanged(type, checkbox) {
        var track = document.getElementById(type + '_track');
        var knob  = document.getElementById(type + '_knob');
        if (checkbox.checked) {
            track.style.background = '#C8102E';
            knob.style.left = '23px';
        } else {
            track.style.background = '#d1d5db';
            knob.style.left = '3px';
        }

        // Show/hide dropdowns
        if (type === 'role') {
            document.getElementById('role_field').style.display = checkbox.checked ? '' : 'none';
            if (!checkbox.checked) document.querySelector('select[name="assign_type"]').value = '';
        }
        if (type === 'user') {
            document.getElementById('user_field').style.display = checkbox.checked ? '' : 'none';
            if (!checkbox.checked) document.querySelector('select[name="assign_user_id"]').value = '';
        }
        // Public is mutually exclusive with Role and User
        if (type === 'public' && checkbox.checked) {
            var roleChk = document.getElementById('assign_role_toggle');
            var userChk = document.getElementById('assign_user_toggle');
            roleChk.checked = false; assignToggleChanged('role', roleChk);
            userChk.checked = false; assignToggleChanged('user', userChk);
        }
    }
</script>
@endpush
