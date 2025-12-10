@extends('layouts.admin')

@section('title', 'Add User')
@section('page-title', 'Add User')

@section('content')
<form action="{{ route('admin.users.store') }}" method="POST" class="card" style="max-width: 640px;">
    @csrf
    <div class="form-group">
        <label class="form-label">Name *</label>
        <input type="text" name="name" class="form-input" value="{{ old('name') }}" required>
        @error('name')<span class="form-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label class="form-label">Email *</label>
        <input type="email" name="email" class="form-input" value="{{ old('email') }}" required>
        @error('email')<span class="form-error">{{ $message }}</span>@enderror
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
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Create User</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
@endsection
