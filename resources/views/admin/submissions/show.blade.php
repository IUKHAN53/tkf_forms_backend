@extends('layouts.admin')

@section('title', 'Submission Details')
@section('page-title', 'Submission #' . $submission->id)

@section('content')
<div class="page-header">
    <div class="page-header-actions">
        <a href="{{ route('admin.forms.show', $submission->form) }}" class="btn btn-secondary">View Form</a>
        <form action="{{ route('admin.submissions.destroy', $submission) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-secondary">Delete</button>
        </form>
    </div>
</div>

<div class="details-grid">
    <div class="card">
        <h3 class="card-title">Submission Info</h3>
        <div class="detail-row">
            <span class="detail-label">Form:</span>
            <span class="detail-value">{{ $submission->form->name }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Submitted By:</span>
            <span class="detail-value">{{ $submission->user->name ?? 'Anonymous' }}</span>
        </div>
        @if($submission->latitude && $submission->longitude)
            <div class="detail-row">
                <span class="detail-label">Location:</span>
                <span class="detail-value">{{ $submission->latitude }}, {{ $submission->longitude }}</span>
            </div>
        @endif
        <div class="detail-row">
            <span class="detail-label">Submitted:</span>
            <span class="detail-value">{{ $submission->created_at->format('F d, Y H:i') }}</span>
        </div>
    </div>

    <div class="card full-width">
        <h3 class="card-title">Form Data</h3>
        <div class="data-list">
            @foreach($submission->data as $key => $value)
                <div class="data-item">
                    <span class="data-key">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                    <span class="data-value">{{ is_array($value) ? json_encode($value) : $value }}</span>
                </div>
            @endforeach
        </div>
    </div>

    @if($submission->media->count() > 0)
        <div class="card full-width">
            <h3 class="card-title">Attachments</h3>
            <div class="media-grid">
                @foreach($submission->media as $media)
                    <div class="media-item">
                        @if(str_starts_with($media->mime_type, 'image/'))
                            <img src="{{ $media->getUrl() }}" alt="{{ $media->file_name }}" class="media-preview">
                        @else
                            <div class="media-file">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                    <polyline points="13 2 13 9 20 9"></polyline>
                                </svg>
                            </div>
                        @endif
                        <div class="media-info">
                            <span class="media-name">{{ $media->file_name }}</span>
                            <a href="{{ $media->getUrl() }}" target="_blank" class="btn btn-sm btn-secondary">Download</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
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
    min-width: 140px;
}

.detail-value {
    color: var(--color-text-primary);
}

.data-list {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-sm);
}

.data-item {
    padding: var(--spacing-md);
    background-color: var(--color-bg-neutral);
    border-radius: var(--radius-sm);
    display: flex;
    gap: var(--spacing-md);
}

.data-key {
    font-weight: 600;
    color: var(--color-text-secondary);
    min-width: 200px;
}

.data-value {
    color: var(--color-text-primary);
}

.media-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: var(--spacing-md);
}

.media-item {
    border: 1px solid var(--color-border);
    border-radius: var(--radius-sm);
    overflow: hidden;
}

.media-preview {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.media-file {
    width: 100%;
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--color-bg-neutral);
    color: var(--color-text-secondary);
}

.media-info {
    padding: var(--spacing-sm);
    display: flex;
    flex-direction: column;
    gap: var(--spacing-xs);
}

.media-name {
    font-size: 13px;
    color: var(--color-text-secondary);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>
@endsection
