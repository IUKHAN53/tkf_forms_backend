@extends('layouts.admin')

@section('title', 'Edit Bridging The Gap')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <h2>Edit Bridging The Gap <code>{{ $bridgingTheGap->unique_id }}</code></h2>
            <p class="text-muted">Update session details</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.bridging-the-gap.show', $bridgingTheGap) }}" class="btn btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Cancel
            </a>
        </div>
    </div>

    <form action="{{ route('admin.bridging-the-gap.update', $bridgingTheGap) }}" method="POST" class="edit-form">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-group">
                <label for="date">Date <span class="required">*</span></label>
                <input type="date" id="date" name="date" class="form-input @error('date') is-invalid @enderror"
                       value="{{ old('date', $bridgingTheGap->date?->format('Y-m-d')) }}" required>
                @error('date')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="district">District <span class="required">*</span></label>
                <input type="text" id="district" name="district" class="form-input @error('district') is-invalid @enderror"
                       value="{{ old('district', $bridgingTheGap->district) }}" required>
                @error('district')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="uc">UC <span class="required">*</span></label>
                <input type="text" id="uc" name="uc" class="form-input @error('uc') is-invalid @enderror"
                       value="{{ old('uc', $bridgingTheGap->uc) }}" required>
                @error('uc')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="fix_site">Fix Site</label>
                <input type="text" id="fix_site" name="fix_site" class="form-input @error('fix_site') is-invalid @enderror"
                       value="{{ old('fix_site', $bridgingTheGap->fix_site) }}">
                @error('fix_site')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="venue">Venue <span class="required">*</span></label>
                <input type="text" id="venue" name="venue" class="form-input @error('venue') is-invalid @enderror"
                       value="{{ old('venue', $bridgingTheGap->venue) }}" required>
                @error('venue')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="participants_males">Male Participants <span class="required">*</span></label>
                <input type="number" id="participants_males" name="participants_males" class="form-input @error('participants_males') is-invalid @enderror"
                       value="{{ old('participants_males', $bridgingTheGap->participants_males) }}" min="0" required>
                @error('participants_males')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="participants_females">Female Participants <span class="required">*</span></label>
                <input type="number" id="participants_females" name="participants_females" class="form-input @error('participants_females') is-invalid @enderror"
                       value="{{ old('participants_females', $bridgingTheGap->participants_females) }}" min="0" required>
                @error('participants_females')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                Save Changes
            </button>
            <a href="{{ route('admin.bridging-the-gap.show', $bridgingTheGap) }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<style>
.edit-form {
    padding: 24px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 24px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-group label {
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-700);
}

.form-group label .required {
    color: #ef4444;
}

.form-input {
    padding: 10px 14px;
    border: 1px solid var(--gray-300);
    border-radius: var(--radius);
    font-size: 14px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-input:focus {
    outline: none;
    border-color: var(--primary-500);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.form-input.is-invalid {
    border-color: #ef4444;
}

.error-message {
    font-size: 12px;
    color: #ef4444;
}

.form-actions {
    display: flex;
    gap: 12px;
    padding-top: 16px;
    border-top: 1px solid var(--gray-100);
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection
