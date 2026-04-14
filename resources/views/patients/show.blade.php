@extends('layouts.app')

@section('title', $patient->full_name . ' - AdvantageHCS Admin')
@section('page-title', $patient->full_name)
@section('page-subtitle', 'MRN: ' . $patient->mrn)

@section('header-actions')
    <a href="{{ route('patients.edit', $patient) }}" class="btn btn-secondary">
        <i class="fas fa-edit"></i> Edit
    </a>
    <a href="{{ route('patients.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
@endsection

@section('content')
<div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px;">

    <!-- Personal Info -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-user" style="color:#C8102E; margin-right:8px;"></i>Personal Information</span>
            <span class="badge {{ $patient->status === 'active' ? 'badge-success' : ($patient->status === 'pending' ? 'badge-warning' : 'badge-secondary') }}">
                {{ ucfirst($patient->status) }}
            </span>
        </div>
        <div class="card-body">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div>
                    <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Full Name</div>
                    <div style="font-weight:600;">{{ $patient->full_name }}</div>
                </div>
                <div>
                    <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">MRN</div>
                    <div style="font-family:monospace; font-weight:600;">{{ $patient->mrn }}</div>
                </div>
                <div>
                    <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Email</div>
                    <div>{{ $patient->email }}</div>
                </div>
                <div>
                    <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Phone</div>
                    <div>{{ $patient->phone ?? '—' }}</div>
                </div>
                <div>
                    <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Date of Birth</div>
                    <div>{{ $patient->date_of_birth ? $patient->date_of_birth->format('M d, Y') : '—' }}
                        @if($patient->age) <span style="color:#6b7280;">({{ $patient->age }}y)</span> @endif
                    </div>
                </div>
                <div>
                    <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Gender</div>
                    <div>{{ $patient->gender ? ucfirst(str_replace('_', ' ', $patient->gender)) : '—' }}</div>
                </div>
                <div style="grid-column:1/-1;">
                    <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Address</div>
                    <div>
                        @if($patient->address)
                            {{ $patient->address }}<br>
                            {{ $patient->city }}{{ $patient->city && $patient->state ? ', ' : '' }}{{ $patient->state }} {{ $patient->zip_code }}
                        @else
                            —
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Insurance Info -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-shield-alt" style="color:#3b82f6; margin-right:8px;"></i>Insurance & Medical</span>
        </div>
        <div class="card-body">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div style="grid-column:1/-1;">
                    <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Insurance Provider</div>
                    <div style="font-weight:600;">{{ $patient->insurance_provider ?? '—' }}</div>
                </div>
                <div>
                    <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Member ID</div>
                    <div style="font-family:monospace;">{{ $patient->insurance_member_id ?? '—' }}</div>
                </div>
                <div>
                    <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Group Number</div>
                    <div style="font-family:monospace;">{{ $patient->insurance_group_number ?? '—' }}</div>
                </div>
                <div style="grid-column:1/-1;">
                    <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Primary Physician</div>
                    <div>{{ $patient->primary_physician ?? '—' }}</div>
                </div>
                @if($patient->notes)
                <div style="grid-column:1/-1;">
                    <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Notes</div>
                    <div style="background:#f9fafb; padding:12px; border-radius:8px; font-size:14px; color:#374151;">{{ $patient->notes }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>

<!-- Form Submissions -->
<div class="card" style="margin-top:24px;">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-wpforms" style="color:#C8102E; margin-right:8px;"></i>Form Submissions</span>
        <span style="font-size:13px; color:#6b7280;">{{ $patient->formSubmissions->count() }} total</span>
    </div>
    @if($patient->formSubmissions->isEmpty())
        <div class="card-body" style="text-align:center; color:#9ca3af; padding:40px;">
            <i class="fas fa-inbox" style="font-size:32px; display:block; margin-bottom:12px;"></i>
            No form submissions yet
        </div>
    @else
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Form</th>
                        <th>Status</th>
                        <th>Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patient->formSubmissions as $submission)
                    <tr>
                        <td style="font-weight:500;">{{ $submission->form->name ?? 'Unknown Form' }}</td>
                        <td>
                            <span class="badge {{ $submission->status === 'reviewed' ? 'badge-success' : ($submission->status === 'pending' ? 'badge-warning' : 'badge-secondary') }}">
                                {{ ucfirst($submission->status) }}
                            </span>
                        </td>
                        <td style="font-size:13px; color:#6b7280;">{{ $submission->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
