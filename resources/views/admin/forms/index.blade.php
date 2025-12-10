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
</style>
@endsection
