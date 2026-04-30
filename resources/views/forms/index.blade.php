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
<style>
/* ── Delete Confirmation Modal ─────────────────────────────── */
#delete-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    z-index: 99998;
    align-items: center;
    justify-content: center;
}
#delete-modal-overlay.open {
    display: flex;
}
#delete-modal {
    background: #fff;
    border-radius: 14px;
    padding: 32px 28px 24px;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    text-align: center;
    animation: modalIn .2s ease;
}
@keyframes modalIn { from { transform: scale(.94); opacity: 0; } to { transform: scale(1); opacity: 1; } }
#delete-modal .modal-icon {
    width: 56px; height: 56px;
    background: #fef2f2;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px;
}
#delete-modal .modal-icon i {
    font-size: 24px;
    color: #dc2626;
}
#delete-modal h3 {
    font-size: 18px;
    font-weight: 700;
    color: #111827;
    margin-bottom: 8px;
}
#delete-modal p {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 24px;
    line-height: 1.5;
}
#delete-modal .modal-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
}
#delete-modal .btn-cancel-modal {
    flex: 1;
    padding: 10px 20px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    background: #fff;
    color: #374151;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background .15s;
}
#delete-modal .btn-cancel-modal:hover { background: #f9fafb; }
#delete-modal .btn-confirm-delete {
    flex: 1;
    padding: 10px 20px;
    border-radius: 8px;
    border: none;
    background: #dc2626;
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background .15s;
}
#delete-modal .btn-confirm-delete:hover { background: #b91c1c; }
</style>

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
                            {{-- Hidden delete form --}}
                            <form id="delete-form-{{ $form->id }}" method="POST" action="{{ route('forms.destroy', $form) }}" style="display:none;">
                                @csrf @method('DELETE')
                            </form>
                            <button type="button"
                                class="btn btn-danger btn-sm"
                                onclick="openDeleteModal({{ $form->id }}, '{{ addslashes($form->name) }}')">
                                <i class="fas fa-trash"></i>
                            </button>
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

{{-- Delete Confirmation Modal --}}
<div id="delete-modal-overlay">
    <div id="delete-modal">
        <div class="modal-icon">
            <i class="fas fa-trash-alt"></i>
        </div>
        <h3>Delete Form</h3>
        <p id="delete-modal-msg">Are you sure you want to delete this form? This action cannot be undone.</p>
        <div class="modal-actions">
            <button class="btn-cancel-modal" onclick="closeDeleteModal()">Cancel</button>
            <button class="btn-confirm-delete" onclick="confirmDelete()">Delete</button>
        </div>
    </div>
</div>

<script>
var _deleteFormId = null;

function openDeleteModal(formId, formName) {
    _deleteFormId = formId;
    document.getElementById('delete-modal-msg').textContent =
        'Are you sure you want to delete "' + formName + '"? This action cannot be undone.';
    document.getElementById('delete-modal-overlay').classList.add('open');
}

function closeDeleteModal() {
    _deleteFormId = null;
    document.getElementById('delete-modal-overlay').classList.remove('open');
}

function confirmDelete() {
    if (_deleteFormId) {
        document.getElementById('delete-form-' + _deleteFormId).submit();
    }
}

// Close modal when clicking outside
document.getElementById('delete-modal-overlay').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDeleteModal();
});
</script>
@endsection
