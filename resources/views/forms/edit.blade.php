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
            <div style="display:flex; gap:12px; margin-top:8px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                <a href="{{ route('forms.show', $form) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
