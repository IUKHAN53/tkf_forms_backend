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

        {{-- Source Selection --}}
        <div class="form-group">
            <label class="form-label">Member Source</label>
            <div style="display: flex; gap: 16px;">
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 500;">
                    <input type="radio" name="source_type" value="new" checked onchange="toggleSource('new')">
                    Create New
                </label>
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 500;">
                    <input type="radio" name="source_type" value="existing" onchange="toggleSource('existing')">
                    Select from Bridging the Gap
                </label>
            </div>
        </div>

        {{-- IIT Team Member Search (hidden by default) --}}
        <div class="form-group" id="participantSearchSection" style="display: none;">
            <label class="form-label">Search IIT Team Member</label>
            <div style="position: relative;">
                <input type="text" id="participantSearch" class="form-input" placeholder="Search IIT members by name or phone (min 2 characters)..." autocomplete="off">
                <input type="hidden" name="participant_id" id="participantId" value="">
                <div id="searchResults" style="display: none; position: absolute; top: 100%; left: 0; right: 0; z-index: 100;
                    background: white; border: 1px solid var(--neutral-200); border-radius: 8px; margin-top: 4px;
                    max-height: 260px; overflow-y: auto; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);"></div>
            </div>
            <div id="selectedParticipant" style="display: none; margin-top: 8px; padding: 10px 14px;
                background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; font-size: 13px; color: #1e40af;"></div>
        </div>

        <div class="form-group">
            <label class="form-label">Name *</label>
            <input type="text" name="name" id="nameInput" class="form-input" value="{{ old('name') }}" required>
            @error('name')<span class="form-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Phone *</label>
            <input type="text" name="phone" id="phoneInput" class="form-input" value="{{ old('phone') }}" placeholder="03XXXXXXXXX" required>
            @error('phone')<span class="form-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">District</label>
            <select name="district" id="districtSelect" class="form-input">
                <option value="">-- Select District --</option>
            </select>
            @error('district')<span class="form-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Union Council</label>
            <select name="uc" id="ucSelect" class="form-input" disabled>
                <option value="">-- Select District first --</option>
            </select>
            @error('uc')<span class="form-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Fix Site</label>
            <select name="fix_site" id="fixSiteSelect" class="form-input" disabled>
                <option value="">-- Select UC first --</option>
            </select>
            @error('fix_site')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        {{-- Password with Generate & Copy --}}
        <div class="form-group">
            <label class="form-label">Password *</label>
            <div style="display: flex; gap: 8px; align-items: center;">
                <input type="text" name="password" id="passwordInput" class="form-input" style="flex: 1;" required
                       placeholder="Enter or generate a password" autocomplete="new-password">
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

        {{-- Password Confirmation (hidden when generated) --}}
        <div class="form-group" id="confirmPasswordSection">
            <label class="form-label">Confirm Password *</label>
            <input type="password" name="password_confirmation" id="passwordConfirmation" class="form-input" required>
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

@push('scripts')
<script>
let searchTimeout = null;

function toggleSource(value) {
    const section = document.getElementById('participantSearchSection');
    const selected = document.getElementById('selectedParticipant');
    if (value === 'existing') {
        section.style.display = 'block';
    } else {
        section.style.display = 'none';
        document.getElementById('participantId').value = '';
        selected.style.display = 'none';
        document.getElementById('participantSearch').value = '';
        document.getElementById('searchResults').style.display = 'none';
    }
}

document.getElementById('participantSearch').addEventListener('input', function () {
    clearTimeout(searchTimeout);
    const query = this.value.trim();
    const results = document.getElementById('searchResults');
    if (query.length < 2) { results.style.display = 'none'; return; }

    searchTimeout = setTimeout(() => {
        fetch(`{{ route('admin.community-members.search-participants') }}?q=${encodeURIComponent(query)}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            results.innerHTML = '';
            if (!data.length) {
                results.innerHTML = '<div style="padding: 12px 14px; font-size: 13px; color: #9ca3af;">No participants found</div>';
            } else {
                data.forEach(p => {
                    const item = document.createElement('div');
                    item.style.cssText = 'padding: 10px 14px; cursor: pointer; border-bottom: 1px solid #f3f4f6;';
                    const locationParts = [p.district, p.uc, p.fix_site].filter(Boolean).map(escHtml).join(' &rsaquo; ');
                    item.innerHTML = `<div style="font-size: 13px; font-weight: 600;">${escHtml(p.name)}</div>
                        <div style="font-size: 12px; color: #6b7280; margin-top: 2px;">${escHtml(p.contact_no)}${p.occupation ? ' &middot; ' + escHtml(p.occupation) : ''}</div>
                        ${locationParts ? `<div style="font-size: 11px; color: #9ca3af; margin-top: 2px;">${locationParts}</div>` : ''}`;
                    item.onmouseenter = () => item.style.background = '#f9fafb';
                    item.onmouseleave = () => item.style.background = '';
                    item.onclick = () => selectParticipant(p);
                    results.appendChild(item);
                });
            }
            results.style.display = 'block';
        });
    }, 300);
});

async function selectParticipant(p) {
    document.getElementById('nameInput').value = p.name || '';
    document.getElementById('phoneInput').value = p.contact_no || '';
    document.getElementById('participantId').value = p.id;

    const el = document.getElementById('selectedParticipant');
    el.innerHTML = `<strong>Selected:</strong> ${escHtml(p.name)} (${escHtml(p.contact_no)}) &mdash; ${escHtml(p.source)}
        <button type="button" onclick="clearParticipant()" style="margin-left: 8px; background: none; border: none; color: #1d4ed8; cursor: pointer; text-decoration: underline; font-size: 12px;">Clear</button>`;
    el.style.display = 'block';
    document.getElementById('searchResults').style.display = 'none';
    document.getElementById('participantSearch').value = '';

    await applyLocationFromParticipant(p);
}

async function applyLocationFromParticipant(p) {
    if (!p.district) return;
    if ([...districtSelect.options].some(o => o.value === p.district)) {
        districtSelect.value = p.district;
    } else {
        return;
    }
    await loadUCs(p.district);
    if (p.uc && [...ucSelect.options].some(o => o.value === p.uc)) {
        ucSelect.value = p.uc;
        await loadFixSites(p.district, p.uc);
        if (p.fix_site && [...fixSiteSelect.options].some(o => o.value === p.fix_site)) {
            fixSiteSelect.value = p.fix_site;
        }
    }
}

function clearParticipant() {
    document.getElementById('participantId').value = '';
    document.getElementById('selectedParticipant').style.display = 'none';
    document.getElementById('nameInput').value = '';
    document.getElementById('phoneInput').value = '';
    districtSelect.value = '';
    ucSelect.innerHTML = '<option value="">-- Select District first --</option>';
    ucSelect.disabled = true;
    fixSiteSelect.innerHTML = '<option value="">-- Select UC first --</option>';
    fixSiteSelect.disabled = true;
}

document.addEventListener('click', function (e) {
    if (!document.getElementById('participantSearchSection').contains(e.target)) {
        document.getElementById('searchResults').style.display = 'none';
    }
});

function generatePassword() {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
    let pw = '';
    for (let i = 0; i < 8; i++) pw += chars[Math.floor(Math.random() * chars.length)];
    document.getElementById('passwordInput').value = pw;
    document.getElementById('passwordGenerated').value = '1';
    document.getElementById('confirmPasswordSection').style.display = 'none';
    document.getElementById('passwordConfirmation').removeAttribute('required');
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
        document.getElementById('passwordConfirmation').setAttribute('required', 'required');
    }
});

function escHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// --- Dependent Dropdowns ---
const API_BASE = '/api/v1/outreach-sites';
const districtSelect = document.getElementById('districtSelect');
const ucSelect = document.getElementById('ucSelect');
const fixSiteSelect = document.getElementById('fixSiteSelect');

async function loadDistricts() {
    try {
        const res = await fetch(`${API_BASE}/districts`);
        const districts = await res.json();
        districts.forEach(d => {
            const opt = document.createElement('option');
            opt.value = d;
            opt.textContent = d;
            if (d === '{{ old("district") }}') opt.selected = true;
            districtSelect.appendChild(opt);
        });
        if ('{{ old("district") }}') {
            await loadUCs('{{ old("district") }}');
        }
    } catch (e) {
        console.error('Failed to load districts:', e);
    }
}

async function loadUCs(district) {
    ucSelect.innerHTML = '<option value="">-- Loading... --</option>';
    ucSelect.disabled = true;
    fixSiteSelect.innerHTML = '<option value="">-- Select UC first --</option>';
    fixSiteSelect.disabled = true;
    if (!district) {
        ucSelect.innerHTML = '<option value="">-- Select District first --</option>';
        return;
    }
    try {
        const res = await fetch(`${API_BASE}/union-councils?district=${encodeURIComponent(district)}`);
        const ucs = await res.json();
        ucSelect.innerHTML = '<option value="">-- Select Union Council --</option>';
        ucs.forEach(uc => {
            const opt = document.createElement('option');
            opt.value = uc;
            opt.textContent = uc;
            if (uc === '{{ old("uc") }}') opt.selected = true;
            ucSelect.appendChild(opt);
        });
        ucSelect.disabled = false;
        if ('{{ old("uc") }}') {
            await loadFixSites(district, '{{ old("uc") }}');
        }
    } catch (e) {
        ucSelect.innerHTML = '<option value="">-- Failed to load --</option>';
    }
}

async function loadFixSites(district, uc) {
    fixSiteSelect.innerHTML = '<option value="">-- Loading... --</option>';
    fixSiteSelect.disabled = true;
    if (!uc) {
        fixSiteSelect.innerHTML = '<option value="">-- Select UC first --</option>';
        return;
    }
    try {
        const res = await fetch(`${API_BASE}/fix-sites?district=${encodeURIComponent(district)}&union_council=${encodeURIComponent(uc)}`);
        const sites = await res.json();
        fixSiteSelect.innerHTML = '<option value="">-- Select Fix Site --</option>';
        sites.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s;
            opt.textContent = s;
            if (s === '{{ old("fix_site") }}') opt.selected = true;
            fixSiteSelect.appendChild(opt);
        });
        fixSiteSelect.disabled = false;
    } catch (e) {
        fixSiteSelect.innerHTML = '<option value="">-- Failed to load --</option>';
    }
}

districtSelect.addEventListener('change', function () {
    loadUCs(this.value);
});

ucSelect.addEventListener('change', function () {
    loadFixSites(districtSelect.value, this.value);
});

loadDistricts();
</script>
@endpush
