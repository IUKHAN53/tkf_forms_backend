@extends('layouts.admin')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<form action="{{ route('admin.users.update', $user) }}" method="POST" class="card" style="max-width: 640px;">
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
        <label class="form-label">Password (leave blank to keep)</label>
        <input type="password" name="password" class="form-input">
        @error('password')<span class="form-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="password_confirmation" class="form-input">
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
@endsection
