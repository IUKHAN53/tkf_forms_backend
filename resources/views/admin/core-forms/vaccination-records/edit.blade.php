@extends('layouts.admin')

@section('title', 'Edit Vaccination Record')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <h2>Edit Vaccination Record <code>{{ $vaccinationRecord->unique_id }}</code></h2>
            <p class="text-muted">Submitted on {{ $vaccinationRecord->created_at->format('M d, Y \a\t h:i A') }}</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.vaccination-records.show', $vaccinationRecord) }}" class="btn btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Cancel
            </a>
        </div>
    </div>

    <form action="{{ route('admin.vaccination-records.update', $vaccinationRecord) }}" method="POST" class="form-container">
        @csrf
        @method('PUT')

        @if($errors->any())
        <div style="background: #fee2e2; border: 1px solid #fca5a5; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px; color: #991b1b;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="form-section-title">Child Information</div>
        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">Child Name *</label>
                <input type="text" name="child_name" class="form-input" style="width: 100%;" value="{{ old('child_name', $vaccinationRecord->child_name) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Father Name *</label>
                <input type="text" name="father_name" class="form-input" style="width: 100%;" value="{{ old('father_name', $vaccinationRecord->father_name) }}" required>
            </div>
        </div>

        <div class="form-grid-3">
            <div class="form-group">
                <label class="form-label">Age</label>
                <input type="text" name="age" class="form-input" style="width: 100%;" value="{{ old('age', $vaccinationRecord->age) }}" placeholder="e.g. 2 years">
            </div>
            <div class="form-group">
                <label class="form-label">Contact Number</label>
                <input type="text" name="contact_number" class="form-input" style="width: 100%;" value="{{ old('contact_number', $vaccinationRecord->contact_number) }}" placeholder="03XXXXXXXXX">
            </div>
            <div class="form-group">
                <label class="form-label">Address</label>
                <input type="text" name="address" class="form-input" style="width: 100%;" value="{{ old('address', $vaccinationRecord->address) }}">
            </div>
        </div>

        <div class="form-section-title" style="margin-top: 24px;">Vaccination Status</div>
        <div class="form-grid-3">
            <div class="form-group">
                <label class="form-label">Category *</label>
                <select name="category" class="form-input" style="width: 100%;" required>
                    <option value="Defaulter" {{ old('category', $vaccinationRecord->category) == 'Defaulter' ? 'selected' : '' }}>Defaulter</option>
                    <option value="Refusal" {{ old('category', $vaccinationRecord->category) == 'Refusal' ? 'selected' : '' }}>Refusal</option>
                    <option value="Zero Dose" {{ old('category', $vaccinationRecord->category) == 'Zero Dose' ? 'selected' : '' }}>Zero Dose</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Vaccinated *</label>
                <select name="vaccinated" class="form-input" style="width: 100%;" required>
                    <option value="YES" {{ old('vaccinated', $vaccinationRecord->vaccinated) == 'YES' ? 'selected' : '' }}>YES</option>
                    <option value="NO" {{ old('vaccinated', $vaccinationRecord->vaccinated) == 'NO' ? 'selected' : '' }}>NO</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Date of Vaccination</label>
                <input type="date" name="date_of_vaccination" class="form-input" style="width: 100%;" value="{{ old('date_of_vaccination', $vaccinationRecord->date_of_vaccination?->format('Y-m-d')) }}">
            </div>
        </div>

        <div class="form-section-title" style="margin-top: 24px;">Location Information</div>
        <div class="form-grid-3">
            <div class="form-group">
                <label class="form-label">District</label>
                <input type="text" name="district" class="form-input" style="width: 100%;" value="{{ old('district', $vaccinationRecord->district) }}">
            </div>
            <div class="form-group">
                <label class="form-label">UC (Union Council)</label>
                <input type="text" name="uc" class="form-input" style="width: 100%;" value="{{ old('uc', $vaccinationRecord->uc) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Fix Site</label>
                <input type="text" name="fix_site" class="form-input" style="width: 100%;" value="{{ old('fix_site', $vaccinationRecord->fix_site) }}">
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.vaccination-records.show', $vaccinationRecord) }}" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
