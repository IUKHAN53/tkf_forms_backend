@extends('layouts.admin')

@section('title', 'Edit User')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card" style="max-width: 800px;">
    <div class="card-header">
        <div class="header-left">
            <h2>Edit User</h2>
            <p class="text-muted">Update user account details</p>
        </div>
    </div>

    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="form-container">
        @csrf
        @method('PUT')
    <div class="form-group">
        <label class="form-label">Name *</label>
        <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
        @error('name')<span class="form-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label class="form-label">Email *</label>
        <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
        @error('email')<span class="form-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-input" value="{{ old('phone', $user->phone) }}" placeholder="03XXXXXXXXX">
        @error('phone')<span class="form-error">{{ $message }}</span>@enderror
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
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update User</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
