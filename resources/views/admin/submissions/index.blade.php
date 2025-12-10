@extends('layouts.admin')

@section('title', 'Submissions')
@section('page-title', 'Form Submissions')

@section('content')
<div class="card">
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Form</th>
                    <th>Submitted By</th>
                    <th>Location</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $submission)
                    <tr>
                        <td><span class="badge badge-info">#{{ $submission->id }}</span></td>
                        <td>{{ $submission->form->name }}</td>
                        <td>{{ $submission->user->name ?? 'Anonymous' }}</td>
                        <td>
                            @if($submission->latitude && $submission->longitude)
                                <span class="badge badge-success">
                                    {{ number_format($submission->latitude, 4) }}, {{ number_format($submission->longitude, 4) }}
                                </span>
                            @else
                                <span class="text-secondary">—</span>
                            @endif
                        </td>
                        <td>{{ $submission->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.submissions.show', $submission) }}" class="btn btn-sm btn-secondary">View</a>
                                <form action="{{ route('admin.submissions.destroy', $submission) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-secondary">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-secondary">No submissions found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="pagination-wrapper">
    {{ $submissions->links() }}
</div>

<style>
.action-buttons {
    display: flex;
    gap: var(--spacing-sm);
}

.pagination-wrapper {
    margin-top: var(--spacing-lg);
}
</style>
@endsection
