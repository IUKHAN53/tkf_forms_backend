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

        {{-- Password with Generate & Copy --}}
        <div class="form-group">
            <label class="form-label">Password (leave blank to keep current)</label>
            <div style="display: flex; gap: 8px; align-items: center;">
                <input type="text" name="password" id="passwordInput" class="form-input" style="flex: 1;"
                       placeholder="Enter new password or generate one" autocomplete="new-password">
                <button type="button" class="btn btn-secondary btn-sm" onclick="generatePassword()" title="Generate random password"
                        style="white-space: nowrap;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: -2px;">
                        <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
                    </svg>
                    Generate
                </button>
                <button type="button" class="btn btn-outline btn-sm" onclick="copyPassword()" title="Copy password"
                        style="white-space: nowrap;" id="copyBtn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: -2px;">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                    </svg>
                    Copy
                </button>
            </div>
            <input type="hidden" name="password_generated" id="passwordGenerated" value="0">
            @error('password')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group" id="confirmPasswordSection">
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

@push('scripts')
<script>
function generatePassword() {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
    let pw = '';
    for (let i = 0; i < 8; i++) pw += chars[Math.floor(Math.random() * chars.length)];
    document.getElementById('passwordInput').value = pw;
    document.getElementById('passwordGenerated').value = '1';
    document.getElementById('confirmPasswordSection').style.display = 'none';
}

function copyPassword() {
    const pw = document.getElementById('passwordInput').value;
    if (!pw) return;
    navigator.clipboard.writeText(pw).then(() => {
        const btn = document.getElementById('copyBtn');
        const orig = btn.innerHTML;
        btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;"><polyline points="20 6 9 17 4 12"/></svg> Copied!';
        btn.style.color = '#059669';
        setTimeout(() => { btn.innerHTML = orig; btn.style.color = ''; }, 2000);
    }).catch(() => {
        const input = document.getElementById('passwordInput');
        input.select();
        document.execCommand('copy');
    });
}

document.getElementById('passwordInput').addEventListener('input', function () {
    if (this.value === '' && document.getElementById('passwordGenerated').value === '1') {
        document.getElementById('passwordGenerated').value = '0';
        document.getElementById('confirmPasswordSection').style.display = 'block';
    }
});
</script>
@endpush
