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
        display: flex;
        align-items: center;
        gap: 36px;
        margin-bottom: 16px;
        flex-wrap: nowrap;
    }
    .assign-toggle-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .assign-toggle-item .toggle-label {
        font-size: 14px;
        font-weight: 500;
        color: #374151;
    }
    /* Toggle switch */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
    }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #d1d5db;
        border-radius: 24px;
        transition: .3s;
    }
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 18px; width: 18px;
        left: 3px; bottom: 3px;
        background-color: white;
        border-radius: 50%;
        transition: .3s;
    }
    .toggle-switch input:checked + .toggle-slider { background-color: #C8102E; }
    .toggle-switch input:checked + .toggle-slider:before { transform: translateX(20px); }
    .section-divider {
        border: none;
        border-top: 1px solid #e5e7eb;
        margin: 20px 0;
    }
    .section-heading {
        font-size: 14px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 14px;
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
                        <label class="toggle-switch">
                            <input type="checkbox" name="assign_role_enabled" id="assign_role_toggle"
                                   value="1" {{ old('assign_role_enabled') ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <span class="toggle-label">Role</span>
                    </div>
                    {{-- User toggle --}}
                    <div class="assign-toggle-item">
                        <label class="toggle-switch">
                            <input type="checkbox" name="assign_user_enabled" id="assign_user_toggle"
                                   value="1" {{ old('assign_user_enabled') ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <span class="toggle-label">User</span>
                    </div>
                    {{-- Public toggle --}}
                    <div class="assign-toggle-item">
                        <label class="toggle-switch">
                            <input type="checkbox" name="assign_public_enabled" id="assign_public_toggle"
                                   value="1" {{ old('assign_public_enabled') ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <span class="toggle-label">Public</span>
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
    // Role toggle
    document.getElementById('assign_role_toggle').addEventListener('change', function () {
        document.getElementById('role_field').style.display = this.checked ? '' : 'none';
        if (!this.checked) document.querySelector('select[name="assign_type"]').value = '';
    });

    // User toggle
    document.getElementById('assign_user_toggle').addEventListener('change', function () {
        document.getElementById('user_field').style.display = this.checked ? '' : 'none';
        if (!this.checked) document.querySelector('select[name="assign_user_id"]').value = '';
    });

    // Public toggle — mutually exclusive with Role/User
    document.getElementById('assign_public_toggle').addEventListener('change', function () {
        if (this.checked) {
            document.getElementById('assign_role_toggle').checked = false;
            document.getElementById('assign_user_toggle').checked = false;
            document.getElementById('role_field').style.display = 'none';
            document.getElementById('user_field').style.display = 'none';
        }
    });
</script>
@endpush
