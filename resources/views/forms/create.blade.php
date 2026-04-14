@extends('layouts.app')

@section('title', 'Create Form - AdvantageHCS Admin')
@section('page-title', 'Create Form')
@section('page-subtitle', 'Set up a new patient form')

@section('header-actions')
    <a href="{{ route('forms.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
@endsection

@section('content')
<div class="card" style="max-width:700px;">
    <div class="card-header">
        <span class="card-title">Form Details</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('forms.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Form Name <span style="color:#ef4444;">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Patient Intake Form" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Describe what this form is for...">{{ old('description') }}</textarea>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control">
                        <option value="">Select Category</option>
                        <option value="intake" {{ old('category') === 'intake' ? 'selected' : '' }}>Patient Intake</option>
                        <option value="consent" {{ old('category') === 'consent' ? 'selected' : '' }}>Consent Form</option>
                        <option value="follow-up" {{ old('category') === 'follow-up' ? 'selected' : '' }}>Follow-up</option>
                        <option value="health-history" {{ old('category') === 'health-history' ? 'selected' : '' }}>Health History</option>
                        <option value="hipaa" {{ old('category') === 'hipaa' ? 'selected' : '' }}>HIPAA</option>
                        <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                    </select>
                </div>
            </div>
            <div style="display:flex; gap:12px; margin-top:8px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Create Form</button>
                <a href="{{ route('forms.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
