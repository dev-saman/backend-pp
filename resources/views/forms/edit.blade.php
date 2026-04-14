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
<div class="card" style="max-width:700px;">
    <div class="card-header">
        <span class="card-title">Form Settings</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('forms.update', $form) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Form Name <span style="color:#ef4444;">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $form->name) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $form->description) }}</textarea>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control">
                        <option value="">Select Category</option>
                        <option value="intake" {{ old('category', $form->category) === 'intake' ? 'selected' : '' }}>Patient Intake</option>
                        <option value="consent" {{ old('category', $form->category) === 'consent' ? 'selected' : '' }}>Consent Form</option>
                        <option value="follow-up" {{ old('category', $form->category) === 'follow-up' ? 'selected' : '' }}>Follow-up</option>
                        <option value="health-history" {{ old('category', $form->category) === 'health-history' ? 'selected' : '' }}>Health History</option>
                        <option value="hipaa" {{ old('category', $form->category) === 'hipaa' ? 'selected' : '' }}>HIPAA</option>
                        <option value="other" {{ old('category', $form->category) === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="draft" {{ old('status', $form->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status', $form->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="archived" {{ old('status', $form->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
            </div>
            <div style="display:flex; gap:12px; margin-top:8px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                <a href="{{ route('forms.show', $form) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
