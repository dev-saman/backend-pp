@extends('layouts.app')

@section('title', 'Appointment Detail - AdvantageHCS Admin')
@section('page-title', 'Appointment Detail')
@section('page-subtitle', 'View appointment information')

@section('header-actions')
    <a href="{{ route('appointments.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Appointments
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title">Appointment #{{ $appointment['id'] }}</span>
        <span class="badge badge-success">{{ ucfirst($appointment['status']) }}</span>
    </div>
    <div class="card-body">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px;">
            <div>
                <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; margin-bottom:4px;">Patient</div>
                <div style="font-weight:600; font-size:16px;">{{ $appointment['patient_name'] }}</div>
            </div>
            <div>
                <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; margin-bottom:4px;">Provider</div>
                <div style="font-weight:600; font-size:16px;">{{ $appointment['doctor'] }}</div>
            </div>
            <div>
                <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; margin-bottom:4px;">Date</div>
                <div>{{ \Carbon\Carbon::parse($appointment['date'])->format('l, F d, Y') }}</div>
            </div>
            <div>
                <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; margin-bottom:4px;">Time</div>
                <div>{{ $appointment['time'] }}</div>
            </div>
            <div>
                <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; margin-bottom:4px;">Type</div>
                <div>{{ $appointment['type'] }}</div>
            </div>
            <div>
                <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; margin-bottom:4px;">Location</div>
                <div>{{ $appointment['location'] }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
