@extends('layouts.admin')

@section('title', 'Form Details')
@section('page-title', $form->name)

@section('content')
<div class="page-header">
    <div class="page-header-actions">
        <a href="{{ route('admin.forms.edit', $form) }}" class="btn btn-primary">Edit Form</a>
        <form action="{{ route('admin.forms.destroy', $form) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this form?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-secondary">Delete</button>
        </form>
    </div>
</div>

<div class="details-grid">
    <!-- Form Info Card -->
    <div class="card">
        <h3 class="card-title">Form Information</h3>
        <div class="detail-row">
            <span class="detail-label">Name:</span>
            <span class="detail-value">{{ $form->name }}</span>
        </div>
        @if($form->description)
            <div class="detail-row">
                <span class="detail-label">Description:</span>
                <span class="detail-value">{{ $form->description }}</span>
            </div>
        @endif
        <div class="detail-row">
            <span class="detail-label">Version:</span>
            <span class="detail-value">{{ $form->version }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Status:</span>
            <span class="detail-value">
                @if($form->is_active)
                    <span class="badge badge-success">Active</span>
                @else
                    <span class="badge badge-error">Inactive</span>
                @endif
            </span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Created:</span>
            <span class="detail-value">{{ $form->created_at->format('F d, Y H:i') }}</span>
        </div>
    </div>

    <!-- Form Fields Card -->
    <div class="card full-width">
        <h3 class="card-title">Form Fields</h3>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Label</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Required</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($form->fields as $field)
                        <tr>
                            <td>{{ $field->order }}</td>
                            <td>{{ $field->label }}</td>
                            <td><code>{{ $field->name }}</code></td>
                            <td><span class="badge badge-info">{{ $field->type->value }}</span></td>
                            <td>
                                @if($field->required)
                                    <span class="badge badge-warning">Required</span>
                                @else
                                    <span class="badge badge-success">Optional</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary">No fields defined</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Submissions Card -->
    <div class="card full-width">
        <div class="card-header">
            <h3 class="card-title">Recent Submissions</h3>
            <a href="{{ route('admin.submissions.index', ['form_id' => $form->id]) }}" class="btn btn-sm btn-secondary">View All</a>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Submitted By</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($form->submissions as $submission)
                        <tr>
                            <td><span class="badge badge-info">#{{ $submission->id }}</span></td>
                            <td>{{ $submission->user->name ?? 'Anonymous' }}</td>
                            <td>{{ $submission->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.submissions.show', $submission) }}" class="btn btn-sm btn-secondary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-secondary">No submissions yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: var(--spacing-lg);
}

.full-width {
    grid-column: 1 / -1;
}

.card-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--color-text-primary);
    margin-bottom: var(--spacing-md);
}

.detail-row {
    display: flex;
    padding: var(--spacing-sm) 0;
    border-bottom: 1px solid var(--color-border);
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: var(--color-text-secondary);
    min-width: 120px;
}

.detail-value {
    color: var(--color-text-primary);
}

code {
    background-color: var(--color-bg-neutral);
    padding: 2px 6px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    font-size: 13px;
}
</style>
@endsection
