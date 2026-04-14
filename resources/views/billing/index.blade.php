@extends('layouts.app')

@section('title', 'Billing - AdvantageHCS Admin')
@section('page-title', 'Billing & Insurance')
@section('page-subtitle', 'View patient billing data fetched from the billing system')

@section('content')

<div style="background:#fffbeb; border:1px solid #fcd34d; border-radius:10px; padding:14px 18px; margin-bottom:24px; display:flex; align-items:center; gap:10px; font-size:14px; color:#92400e;">
    <i class="fas fa-info-circle"></i>
    <span>Billing data is fetched from your external billing system. Configure <code>BILLING_API_URL</code> and <code>BILLING_API_KEY</code> in your <code>.env</code> file to connect to live data.</span>
</div>

<!-- Stats Row -->
<div class="stats-grid" style="grid-template-columns:repeat(4,1fr);">
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-dollar-sign"></i></div>
        <div class="stat-info">
            <div class="stat-value">${{ number_format($stats['total_outstanding'], 2) }}</div>
            <div class="stat-label">Total Outstanding</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow"><i class="fas fa-clock"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['pending_claims'] }}</div>
            <div class="stat-label">Pending Claims</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info">
            <div class="stat-value">${{ number_format($stats['paid_this_month'], 2) }}</div>
            <div class="stat-label">Paid This Month</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-shield-alt"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['insurance_pending'] }}</div>
            <div class="stat-label">Insurance Pending</div>
        </div>
    </div>
</div>

<!-- Billing Table -->
<div class="card" style="margin-top:24px;">
    <div class="card-header">
        <span class="card-title">Patient Billing Records</span>
        <div style="display:flex; gap:8px;">
            <select class="form-control" style="width:160px; padding:8px 12px; font-size:13px;">
                <option>All Statuses</option>
                <option>Due</option>
                <option>Paid</option>
                <option>Insurance Pending</option>
            </select>
        </div>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Statement</th>
                    <th>Date</th>
                    <th>Patient Responsibility</th>
                    <th>Insurance Pending</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($billingRecords as $record)
                <tr>
                    <td style="font-weight:600;">{{ $record['patient_name'] }}</td>
                    <td style="font-size:13px; font-family:monospace; color:#6b7280;">{{ $record['statement_id'] }}</td>
                    <td style="font-size:13px;">{{ \Carbon\Carbon::parse($record['date'])->format('M d, Y') }}</td>
                    <td style="font-weight:600; color:{{ $record['patient_responsibility'] > 0 ? '#C8102E' : '#374151' }};">
                        ${{ number_format($record['patient_responsibility'], 2) }}
                    </td>
                    <td style="font-size:13px; color:#6b7280;">${{ number_format($record['insurance_pending'], 2) }}</td>
                    <td style="font-size:13px;">
                        @if($record['due_date'])
                            {{ \Carbon\Carbon::parse($record['due_date'])->format('M d, Y') }}
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @if($record['status'] === 'paid')
                            <span class="badge badge-success">Paid</span>
                        @elseif($record['status'] === 'due')
                            <span class="badge badge-warning">Due</span>
                        @elseif($record['status'] === 'overdue')
                            <span class="badge badge-danger">Overdue</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($record['status']) }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('billing.show', $record['id']) }}" class="btn btn-secondary btn-sm">
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
