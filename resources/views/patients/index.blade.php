@extends('layouts.app')

@section('title', 'Patients - AdvantageHCS Admin')
@section('page-title', 'Patients')
@section('page-subtitle', 'Manage and view all patient records')

@section('header-actions')
    {{-- Patients are read-only from the external AHCS database --}}
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" class="search-bar" style="margin-bottom:0; flex:1;">
            <div class="search-input-wrap">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, MRN...">
            </div>
            <select name="status" class="form-control" style="width:160px; padding:10px 14px;">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            </select>
            <button type="submit" class="btn btn-secondary">
                <i class="fas fa-filter"></i> Filter
            </button>
            @if(request('search') || request('status'))
                <a href="{{ route('patients.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>MRN</th>
                    <th>Date of Birth</th>
                    <th>Phone</th>
                    <th>Insurance</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $patient)
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $patient->full_name }}</div>
                        <div style="font-size:12px; color:#6b7280;">{{ $patient->email }}</div>
                    </td>
                    <td style="font-size:13px; color:#6b7280; font-family:monospace;">{{ $patient->mrn }}</td>
                    <td style="font-size:13px;">
                        {{ $patient->date_of_birth ? $patient->date_of_birth->format('M d, Y') : '—' }}
                        @if($patient->age)
                            <span style="color:#6b7280;">({{ $patient->age }}y)</span>
                        @endif
                    </td>
                    <td style="font-size:13px;">{{ $patient->phone ?? '—' }}</td>
                    <td style="font-size:13px;">{{ $patient->insurance_provider ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $patient->status === 'active' ? 'badge-success' : ($patient->status === 'pending' ? 'badge-warning' : 'badge-secondary') }}">
                            {{ ucfirst($patient->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('patients.show', $patient) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:48px; color:#9ca3af;">
                        <i class="fas fa-users" style="font-size:36px; display:block; margin-bottom:12px;"></i>
                        No patients found in the AHCS system.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($patients->hasPages())
        <div class="pagination">
            {{ $patients->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
