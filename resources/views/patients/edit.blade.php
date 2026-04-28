@extends('layouts.app')

@section('title', 'Edit ' . $patient->full_name . ' - AdvantageHCS Admin')
@section('page-title', 'Edit Patient')
@section('page-subtitle', 'Update patient record for ' . $patient->full_name)

@section('header-actions')
    <a href="{{ route('patients.show', $patient) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title">Patient Information — MRN: {{ $patient->mrn }}</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('patients.update', $patient) }}">
            @csrf
            @method('PUT')

            <h3 style="font-size:15px; font-weight:600; margin-bottom:16px; color:#374151;">Personal Details</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">First Name <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $patient->first_name) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $patient->last_name) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address <span style="color:#ef4444;">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $patient->email) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" name="phone" class="form-control" value="{{ old('phone', $patient->phone) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $patient->date_of_birth?->format('Y-m-d')) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-control">
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender', $patient->gender) === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $patient->gender) === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender', $patient->gender) === 'other' ? 'selected' : '' }}>Other</option>
                        <option value="prefer_not_to_say" {{ old('gender', $patient->gender) === 'prefer_not_to_say' ? 'selected' : '' }}>Prefer not to say</option>
                    </select>
                </div>
            </div>

            <hr style="border:none; border-top:1px solid #e5e7eb; margin:24px 0;">

            <h3 style="font-size:15px; font-weight:600; margin-bottom:16px; color:#374151;">Address</h3>
            <div class="form-group">
                <label class="form-label">Street Address</label>
                <input type="text" name="address" class="form-control" value="{{ old('address', $patient->address) }}">
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city', $patient->city) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">State</label>
                    <input type="text" name="state" class="form-control" value="{{ old('state', $patient->state) }}" maxlength="2">
                </div>
                <div class="form-group">
                    <label class="form-label">ZIP Code</label>
                    <input type="text" name="zip_code" class="form-control" value="{{ old('zip_code', $patient->zip_code) }}">
                </div>
            </div>

            <hr style="border:none; border-top:1px solid #e5e7eb; margin:24px 0;">

            <h3 style="font-size:15px; font-weight:600; margin-bottom:16px; color:#374151;">Insurance & Medical</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Insurance Provider</label>
                    <input type="text" name="insurance_provider" class="form-control" value="{{ old('insurance_provider', $patient->insurance_provider) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Member ID</label>
                    <input type="text" name="insurance_member_id" class="form-control" value="{{ old('insurance_member_id', $patient->insurance_member_id) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Group Number</label>
                    <input type="text" name="insurance_group_number" class="form-control" value="{{ old('insurance_group_number', $patient->insurance_group_number) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Primary Physician</label>
                    <input type="text" name="primary_physician" class="form-control" value="{{ old('primary_physician', $patient->primary_physician) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="active" {{ old('status', $patient->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="pending" {{ old('status', $patient->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="inactive" {{ old('status', $patient->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="4">{{ old('notes', $patient->notes) }}</textarea>
            </div>

            <div style="display:flex; gap:12px; margin-top:8px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="{{ route('patients.show', $patient) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
