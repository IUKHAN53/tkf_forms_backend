@extends('layouts.admin')

@section('title', 'Add Outreach Site')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card" style="max-width: 800px;">
    <div class="card-header">
        <div class="header-left">
            <h2>Add Outreach Site</h2>
            <p class="text-muted">Create a new outreach site</p>
        </div>
    </div>

    <form action="{{ route('admin.outreach-sites.store') }}" method="POST" class="form-container">
        @csrf

        <div class="form-group">
            <label class="form-label">District *</label>
            <input type="text" name="district" class="form-input" value="{{ old('district') }}" required>
            @error('district')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Union Council *</label>
            <input type="text" name="union_council" class="form-input" value="{{ old('union_council') }}" required>
            @error('union_council')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Fix Site *</label>
            <input type="text" name="fix_site" class="form-input" value="{{ old('fix_site') }}" required>
            @error('fix_site')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Outreach Site *</label>
            <input type="text" name="outreach_site" class="form-input" value="{{ old('outreach_site') }}" required>
            @error('outreach_site')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Coordinates</label>
            <input type="text" name="coordinates" class="form-input" value="{{ old('coordinates') }}" placeholder="e.g., 24.8607,67.0011">
            @error('coordinates')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Comments</label>
            <textarea name="comments" class="form-input" rows="3">{{ old('comments') }}</textarea>
            @error('comments')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Site</button>
            <a href="{{ route('admin.outreach-sites.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
