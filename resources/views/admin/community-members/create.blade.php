@extends('layouts.admin')

@section('title', 'Add Community Member')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card" style="max-width: 800px;">
    <div class="card-header">
        <div class="header-left">
            <h2>Add Community Member</h2>
            <p class="text-muted">Create a new CLM Tracker field worker account</p>
        </div>
    </div>

    <form action="{{ route('admin.community-members.store') }}" method="POST" class="form-container">
        @csrf
        <div class="form-group">
            <label class="form-label">Name *</label>
            <input type="text" name="name" class="form-input" value="{{ old('name') }}" required>
            @error('name')<span class="form-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Phone *</label>
            <input type="text" name="phone" class="form-input" value="{{ old('phone') }}" placeholder="03XXXXXXXXX" required>
            @error('phone')<span class="form-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">District</label>
            <input type="text" name="district" class="form-input" value="{{ old('district') }}">
            @error('district')<span class="form-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Union Council</label>
            <input type="text" name="uc" class="form-input" value="{{ old('uc') }}">
            @error('uc')<span class="form-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Fix Site</label>
            <input type="text" name="fix_site" class="form-input" value="{{ old('fix_site') }}">
            @error('fix_site')<span class="form-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Password *</label>
            <input type="password" name="password" class="form-input" required>
            @error('password')<span class="form-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Confirm Password *</label>
            <input type="password" name="password_confirmation" class="form-input" required>
        </div>
        <div class="form-group">
            <label class="form-label" style="display: flex; align-items: center; gap: 8px;">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') === '1' ? 'checked' : '' }}>
                Active Account
            </label>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Member</button>
            <a href="{{ route('admin.community-members.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
