@extends('layouts.app')
@section('title', 'Edit Form - AdvantageHCS Admin')
@section('page-title', 'Edit Form')
@section('page-subtitle', 'Update form settings for: ' . $form->name)

@section('header-actions')
    <a href="{{ route('forms.show', $form) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Builder
    </a>
@endsection

@section('content')
<style>
    .af-toggle-switch { position: relative; display: inline-block; width: 44px; height: 24px; cursor: pointer; vertical-align: middle; }
    .af-toggle-switch input { opacity: 0; width: 0; height: 0; position: absolute; }
    .af-toggle-track { position: absolute; inset: 0; border-radius: 24px; transition: background .3s; }
    .af-toggle-knob  { position: absolute; height: 18px; width: 18px; bottom: 3px; border-radius: 50%; background: #fff; transition: left .3s; display: block; }
</style>

<div class="card" style="max-width:700px;">
    <div class="card-header">
        <span class="card-title">Form Details</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('forms.update', $form) }}">
            @csrf
            @method('PUT')

            {{-- Single hidden field that holds the assign_type value: role | user | public --}}
            @php
                $currentAssignType = old('assign_type', $form->assign_type ?? 'role');
            @endphp
            <input type="hidden" name="assign_type" id="assign_type_hidden" value="{{ $currentAssignType }}">

            {{-- Form Name --}}
            <div class="form-group">
                <label class="form-label">Form Name <span style="color:#ef4444;">*</span></label>
                <input type="text" name="name" class="form-control"
                       value="{{ old('name', $form->name) }}"
                       placeholder="e.g. Patient Intake Form" required>
            </div>

            {{-- Description --}}
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"
                          placeholder="Describe what this form is for...">{{ old('description', $form->description) }}</textarea>
            </div>

            {{-- Success Message --}}
            <div class="form-group">
                <label class="form-label">Success Message</label>
                <textarea name="success_msg" class="form-control" rows="3"
                          placeholder="Message shown after successful form submission...">{{ old('success_msg', $form->success_msg) }}</textarea>
            </div>

            {{-- Thanks Message --}}
            <div class="form-group">
                <label class="form-label">Thanks Message</label>
                <textarea name="thanks_msg" class="form-control" rows="3"
                          placeholder="Message shown on the thank-you page...">{{ old('thanks_msg', $form->thanks_msg) }}</textarea>
            </div>

            {{-- Assign Form --}}
            <div class="form-group">
                <label class="form-label" style="font-weight:700;color:#374151;display:block;margin-bottom:12px;">Assign Form</label>
                <div style="display:flex;align-items:center;gap:40px;flex-wrap:nowrap;margin-bottom:16px;">

                    {{-- Role toggle --}}
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="font-size:14px;font-weight:500;color:#374151;">Role</span>
                        <label class="af-toggle-switch">
                            <input type="checkbox" id="assign_role_chk"
                                   {{ $currentAssignType === 'role' ? 'checked' : '' }}
                                   onchange="afToggleChanged('role', this)">
                            <span class="af-toggle-track" id="role_track"
                                  style="background:{{ $currentAssignType === 'role' ? '#C8102E' : '#d1d5db' }};">
                                <span class="af-toggle-knob" id="role_knob"
                                      style="left:{{ $currentAssignType === 'role' ? '23px' : '3px' }};"></span>
                            </span>
                        </label>
                    </div>

                    {{-- User toggle --}}
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="font-size:14px;font-weight:500;color:#374151;">User</span>
                        <label class="af-toggle-switch">
                            <input type="checkbox" id="assign_user_chk"
                                   {{ $currentAssignType === 'user' ? 'checked' : '' }}
                                   onchange="afToggleChanged('user', this)">
                            <span class="af-toggle-track" id="user_track"
                                  style="background:{{ $currentAssignType === 'user' ? '#C8102E' : '#d1d5db' }};">
                                <span class="af-toggle-knob" id="user_knob"
                                      style="left:{{ $currentAssignType === 'user' ? '23px' : '3px' }};"></span>
                            </span>
                        </label>
                    </div>

                    {{-- Public toggle --}}
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="font-size:14px;font-weight:500;color:#374151;">Public</span>
                        <label class="af-toggle-switch">
                            <input type="checkbox" id="assign_public_chk"
                                   {{ $currentAssignType === 'public' ? 'checked' : '' }}
                                   onchange="afToggleChanged('public', this)">
                            <span class="af-toggle-track" id="public_track"
                                  style="background:{{ $currentAssignType === 'public' ? '#C8102E' : '#d1d5db' }};">
                                <span class="af-toggle-knob" id="public_knob"
                                      style="left:{{ $currentAssignType === 'public' ? '23px' : '3px' }};"></span>
                            </span>
                        </label>
                    </div>

                </div>

                {{-- Role dropdown (visible when assign_type = role) --}}
                <div id="role_field" style="{{ $currentAssignType === 'role' ? '' : 'display:none;' }}">
                    <div class="form-group" style="margin-top:10px;">
                        <label class="form-label">Role</label>
                        <select name="assign_role_value" class="form-control">
                            <option value="">Select Role</option>
                            <option value="admin"       {{ old('assign_role_value', $form->assign_type === 'role' ? $form->assign_user_id : '') === 'admin'       ? 'selected' : '' }}>Admin</option>
                            <option value="super_admin" {{ old('assign_role_value', $form->assign_type === 'role' ? $form->assign_user_id : '') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                            <option value="user"        {{ old('assign_role_value', $form->assign_type === 'role' ? $form->assign_user_id : '') === 'user'        ? 'selected' : '' }}>User</option>
                        </select>
                    </div>
                </div>

                {{-- User dropdown (visible when assign_type = user) --}}
                <div id="user_field" style="{{ $currentAssignType === 'user' ? '' : 'display:none;' }}">
                    <div class="form-group" style="margin-top:10px;">
                        <label class="form-label">Select User</label>
                        <select name="assign_user_id" class="form-control">
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ old('assign_user_id', $form->assign_user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Submit buttons --}}
            <div style="display:flex; gap:12px; margin-top:8px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                <a href="{{ route('forms.show', $form) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

<script>
    var AF_TYPES = ['role', 'user', 'public'];

    function afToggleChanged(type, checkbox) {
        var on = checkbox.checked;

        // Mutually exclusive: turn off all others when this one turns ON
        if (on) {
            AF_TYPES.forEach(function(t) {
                if (t !== type) {
                    var otherChk = document.getElementById('assign_' + t + '_chk');
                    if (otherChk && otherChk.checked) {
                        otherChk.checked = false;
                        afSetVisual(t, false);
                        afHideField(t);
                    }
                }
            });
            document.getElementById('assign_type_hidden').value = type;
        } else {
            document.getElementById('assign_type_hidden').value = '';
        }

        afSetVisual(type, on);

        if (type === 'role') {
            document.getElementById('role_field').style.display = on ? '' : 'none';
            if (!on) document.querySelector('select[name="assign_role_value"]').value = '';
        }
        if (type === 'user') {
            document.getElementById('user_field').style.display = on ? '' : 'none';
            if (!on) document.querySelector('select[name="assign_user_id"]').value = '';
        }
    }

    function afSetVisual(type, on) {
        var track = document.getElementById(type + '_track');
        var knob  = document.getElementById(type + '_knob');
        if (track) track.style.background = on ? '#C8102E' : '#d1d5db';
        if (knob)  knob.style.left        = on ? '23px' : '3px';
    }

    function afHideField(type) {
        if (type === 'role') {
            document.getElementById('role_field').style.display = 'none';
            document.querySelector('select[name="assign_role_value"]').value = '';
        }
        if (type === 'user') {
            document.getElementById('user_field').style.display = 'none';
            document.querySelector('select[name="assign_user_id"]').value = '';
        }
    }
</script>
