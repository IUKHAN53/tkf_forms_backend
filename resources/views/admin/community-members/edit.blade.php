@extends('layouts.admin')

@section('title', 'Edit Community Member')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card" style="max-width: 800px;">
    <div class="card-header">
        <div class="header-left">
            <h2>Edit Community Member</h2>
            <p class="text-muted">Update field worker account details</p>
        </div>
    </div>

    <form action="{{ route('admin.community-members.update', $member) }}" method="POST" class="form-container">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label class="form-label">Name *</label>
            <input type="text" name="name" class="form-input" value="{{ old('name', $member->name) }}" required>
            @error('name')<span class="form-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Phone *</label>
            <input type="text" name="phone" class="form-input" value="{{ old('phone', $member->phone) }}" required>
            @error('phone')<span class="form-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">District</label>
            <input type="text" name="district" class="form-input" value="{{ old('district', $member->district) }}">
            @error('district')<span class="form-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Union Council</label>
            <input type="text" name="uc" class="form-input" value="{{ old('uc', $member->uc) }}">
            @error('uc')<span class="form-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Fix Site</label>
            <input type="text" name="fix_site" class="form-input" value="{{ old('fix_site', $member->fix_site) }}">
            @error('fix_site')<span class="form-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Password (leave blank to keep)</label>
            <input type="password" name="password" class="form-input">
            @error('password')<span class="form-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-input">
        </div>
        <div class="form-group">
            <label class="form-label" style="display: flex; align-items: center; gap: 8px;">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $member->is_active ? '1' : '0') === '1' ? 'checked' : '' }}>
                Active Account
            </label>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Member</button>
            <a href="{{ route('admin.community-members.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
