@extends('layouts.app')

@section('title', 'New Message - AdvantageHCS Admin')
@section('page-title', 'New Message')
@section('page-subtitle', 'Compose and send a new message')

@section('header-actions')
    <a href="{{ route('messages.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
@endsection

@section('content')
<div class="card" style="max-width:700px;">
    <div class="card-header">
        <span class="card-title">Compose Message</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('messages.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Category</label>
                <select name="category" class="form-control">
                    <option value="">General</option>
                    <option value="clinical">Clinical</option>
                    <option value="billing">Billing</option>
                    <option value="appointment">Appointment</option>
                    <option value="lab-results">Lab Results</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Subject <span style="color:#ef4444;">*</span></label>
                <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" placeholder="Message subject..." required>
            </div>
            <div class="form-group">
                <label class="form-label">Message <span style="color:#ef4444;">*</span></label>
                <textarea name="body" class="form-control" rows="8" placeholder="Type your message here..." required>{{ old('body') }}</textarea>
            </div>
            <div style="display:flex; gap:12px; margin-top:8px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Send Message</button>
                <a href="{{ route('messages.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
