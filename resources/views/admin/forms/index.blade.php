@extends('layouts.admin')

@section('title', 'Forms')
@section('page-title', 'Forms')

@section('content')
<div class="page-header">
    <div class="page-header-actions">
        <a href="{{ route('admin.forms.create') }}" class="btn btn-primary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Create Form
        </a>
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Version</th>
                    <th>Status</th>
                    <th>Submissions</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($forms as $form)
                    <tr>
                        <td><span class="badge badge-info">#{{ $form->id }}</span></td>
                        <td>
                            <div>
                                <strong>{{ $form->name }}</strong>
                                @if($form->description)
                                    <p class="text-secondary" style="font-size: 13px; margin-top: 4px;">{{ Str::limit($form->description, 50) }}</p>
                                @endif
                            </div>
                        </td>
                        <td>{{ $form->version }}</td>
                        <td>
                            @if($form->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-error">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $form->submissions_count }}</td>
                        <td>{{ $form->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.forms.show', $form) }}" class="btn btn-sm btn-secondary">View</a>
                                <a href="{{ route('admin.forms.edit', $form) }}" class="btn btn-sm btn-secondary">Edit</a>
                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $form->id }}, '{{ addslashes($form->name) }}', {{ $form->submissions_count }})">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-secondary">No forms found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="pagination-wrapper">
    {{ $forms->links() }}
</div>

<!-- Delete Confirmation Modal -->
<dialog id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>⚠️ Confirm Delete</h3>
            <button type="button" onclick="closeDeleteModal()" class="modal-close">&times;</button>
        </div>
        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-body">
                <p id="deleteMessage" class="text-warning"></p>
                <p class="text-muted" style="margin-top: 12px;">This action cannot be undone. All submissions and form fields will be permanently deleted.</p>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeDeleteModal()" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete Form</button>
            </div>
        </form>
    </div>
</dialog>

<style>
.page-header {
    margin-bottom: var(--spacing-lg);
}

.page-header-actions {
    display: flex;
    gap: var(--spacing-sm);
}

.btn svg {
    margin-right: 8px;
}

.action-buttons {
    display: flex;
    gap: var(--spacing-sm);
}

.pagination-wrapper {
    margin-top: var(--spacing-lg);
}

.modal {
    padding: 0;
    border: none;
    border-radius: 12px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    max-width: 480px;
    width: 90%;
}

.modal::backdrop {
    background: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background: var(--color-bg-elevated);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--spacing-lg);
    border-bottom: 1px solid var(--color-border);
}

.modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: var(--color-text-secondary);
}

.modal-body {
    padding: var(--spacing-lg);
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: var(--spacing-sm);
    padding: var(--spacing-lg);
    border-top: 1px solid var(--color-border);
}

.text-warning {
    color: #f59e0b;
    font-weight: 500;
}
</style>

<script>
function confirmDelete(formId, formName, submissionsCount) {
    const message = `Are you sure you want to delete "${formName}"?`;
    const submissionInfo = submissionsCount > 0
        ? `\n\nThis will also delete ${submissionsCount} submission(s).`
        : '';

    document.getElementById('deleteMessage').textContent = message + submissionInfo;
    document.getElementById('deleteForm').action = `/admin/forms/${formId}`;
    document.getElementById('deleteModal').showModal();
}

function closeDeleteModal() {
    document.getElementById('deleteModal').close();
}
</script>
@endsection
