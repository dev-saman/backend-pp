@extends('layouts.app')

@section('title', 'Forms - AdvantageHCS Admin')
@section('page-title', 'Forms')
@section('page-subtitle', 'Manage patient intake forms and documents')

@section('header-actions')
    <a href="{{ route('forms.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Create Form
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" class="search-bar" style="margin-bottom:0; flex:1;">
            <div class="search-input-wrap">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search forms...">
            </div>
            <select name="status" class="form-control" style="width:160px; padding:10px 14px;">
                <option value="">All Statuses</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
            </select>
            <button type="submit" class="btn btn-secondary"><i class="fas fa-filter"></i> Filter</button>
        </form>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Form Name</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Submissions</th>
                    <th>Created By</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($forms as $form)
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $form->name }}</div>
                        @if($form->description)
                            <div style="font-size:12px; color:#6b7280;">{{ Str::limit($form->description, 60) }}</div>
                        @endif
                    </td>
                    <td style="font-size:13px;">{{ $form->category ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $form->status === 'active' ? 'badge-success' : ($form->status === 'draft' ? 'badge-warning' : 'badge-secondary') }}">
                            {{ ucfirst($form->status) }}
                        </span>
                    </td>
                    <td style="font-size:13px; color:#6b7280;">{{ $form->submission_count }}</td>
                    <td style="font-size:13px; color:#6b7280;">{{ $form->creator->name ?? '—' }}</td>
                    <td style="font-size:12px; color:#6b7280;">{{ $form->created_at->format('M d, Y') }}</td>
                    <td>
                        <div style="display:flex; gap:8px;">
                            <a href="{{ route('forms.show', $form) }}" class="btn btn-secondary btn-sm"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('forms.edit', $form) }}" class="btn btn-secondary btn-sm"><i class="fas fa-edit"></i></a>
                            <form method="POST" action="{{ route('forms.destroy', $form) }}" onsubmit="return confirm('Delete this form?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:48px; color:#9ca3af;">
                        <i class="fas fa-wpforms" style="font-size:36px; display:block; margin-bottom:12px;"></i>
                        No forms yet. <a href="{{ route('forms.create') }}" style="color:#C8102E;">Create your first form</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($forms->hasPages())
        <div class="pagination">{{ $forms->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
