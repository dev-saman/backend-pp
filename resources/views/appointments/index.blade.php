@extends('layouts.app')

@section('title', 'Appointments - AdvantageHCS Admin')
@section('page-title', 'Appointments')
@section('page-subtitle', 'View patient appointments fetched from the booking system')

@section('content')



<div style="background:#fffbeb; border:1px solid #fcd34d; border-radius:10px; padding:14px 18px; margin-bottom:24px; display:flex; align-items:center; gap:10px; font-size:14px; color:#92400e;">
    <i class="fas fa-info-circle"></i>
    <span>Appointments are fetched from your external booking system. Configure <code>APPOINTMENT_API_URL</code> and <code>APPOINTMENT_API_KEY</code> in your <code>.env</code> file to connect to your live data.</span>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">All Appointments</span>
        <div style="display:flex; gap:8px;">
            <select class="form-control" style="width:160px; padding:8px 12px; font-size:13px;">
                <option>All Statuses</option>
                <option>Confirmed</option>
                <option>Pending</option>
                <option>Action Required</option>
            </select>
        </div>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Doctor / Provider</th>
                    <th>Date & Time</th>
                    <th>Type</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $apt)
                <tr>
                    <td style="font-weight:600;">{{ $apt['patient_name'] }}</td>
                    <td style="font-size:13px; color:#374151;">{{ $apt['doctor'] }}</td>
                    <td>
                        <div style="font-weight:500;">{{ \Carbon\Carbon::parse($apt['date'])->format('M d, Y') }}</div>
                        <div style="font-size:12px; color:#6b7280;">{{ $apt['time'] }}</div>
                    </td>
                    <td>
                        @if($apt['type'] === 'Telehealth')
                            <span class="badge badge-info"><i class="fas fa-video" style="margin-right:4px;"></i>Telehealth</span>
                        @else
                            <span class="badge badge-secondary"><i class="fas fa-hospital" style="margin-right:4px;"></i>In-Person</span>
                        @endif
                    </td>
                    <td style="font-size:13px; color:#6b7280;">{{ $apt['location'] }}</td>
                    <td>
                        @if($apt['status'] === 'confirmed')
                            <span class="badge badge-success">Confirmed</span>
                        @elseif($apt['status'] === 'action_required')
                            <span class="badge badge-warning">Action Required</span>
                        @elseif($apt['status'] === 'pending')
                            <span class="badge badge-secondary">Pending</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($apt['status']) }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('appointments.show', $apt['id']) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
