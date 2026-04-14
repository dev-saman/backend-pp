@extends('layouts.app')

@section('title', 'Billing Detail - AdvantageHCS Admin')
@section('page-title', 'Billing Detail')
@section('page-subtitle', 'Statement ' . $record['statement_id'])

@section('header-actions')
    <a href="{{ route('billing.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Billing
    </a>
@endsection

@section('content')
<div style="display:grid; grid-template-columns:1fr 1fr; gap:24px;">
    <div class="card">
        <div class="card-header">
            <span class="card-title">Statement Details</span>
            @if($record['status'] === 'paid')
                <span class="badge badge-success">Paid</span>
            @elseif($record['status'] === 'due')
                <span class="badge badge-warning">Due</span>
            @else
                <span class="badge badge-secondary">{{ ucfirst($record['status']) }}</span>
            @endif
        </div>
        <div class="card-body">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div>
                    <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; margin-bottom:4px;">Patient</div>
                    <div style="font-weight:600;">{{ $record['patient_name'] }}</div>
                </div>
                <div>
                    <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; margin-bottom:4px;">Statement ID</div>
                    <div style="font-family:monospace;">{{ $record['statement_id'] }}</div>
                </div>
                <div>
                    <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; margin-bottom:4px;">Statement Date</div>
                    <div>{{ \Carbon\Carbon::parse($record['date'])->format('M d, Y') }}</div>
                </div>
                <div>
                    <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; margin-bottom:4px;">Due Date</div>
                    <div>{{ $record['due_date'] ? \Carbon\Carbon::parse($record['due_date'])->format('M d, Y') : '—' }}</div>
                </div>
                <div>
                    <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; margin-bottom:4px;">Patient Responsibility</div>
                    <div style="font-size:20px; font-weight:700; color:#C8102E;">${{ number_format($record['patient_responsibility'], 2) }}</div>
                </div>
                <div>
                    <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; margin-bottom:4px;">Insurance Pending</div>
                    <div style="font-size:20px; font-weight:700; color:#374151;">${{ number_format($record['insurance_pending'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Insurance Information</span>
        </div>
        <div class="card-body">
            <div style="display:grid; gap:16px;">
                <div>
                    <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; margin-bottom:4px;">Insurance Provider</div>
                    <div style="font-weight:600;">{{ $record['insurance_provider'] ?? '—' }}</div>
                </div>
                <div>
                    <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; margin-bottom:4px;">Plan</div>
                    <div>{{ $record['insurance_plan'] ?? '—' }}</div>
                </div>
                <div>
                    <div style="font-size:12px; color:#6b7280; font-weight:500; text-transform:uppercase; margin-bottom:4px;">Member ID</div>
                    <div style="font-family:monospace;">{{ $record['member_id'] ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
