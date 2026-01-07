@extends('layouts.admin')

@section('title', 'Add Child Line List Entry')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card" style="max-width: 900px;">
    <div class="card-header">
        <div class="header-left">
            <h2>Add Child Line List Entry</h2>
            <p class="text-muted">Create a new child line list entry manually</p>
        </div>
    </div>

    <form action="{{ route('admin.child-line-list.store') }}" method="POST" class="form-container">
        @csrf

        <!-- Location Information -->
        <div style="margin-bottom: 32px;">
            <h3 class="form-section-title">Location Information</h3>
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">Division *</label>
                    <input type="text" name="division" class="form-input" value="{{ old('division') }}" required>
                    @error('division')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">District *</label>
                    <input type="text" name="district" class="form-input" value="{{ old('district') }}" required>
                    @error('district')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Town *</label>
                    <input type="text" name="town" class="form-input" value="{{ old('town') }}" required>
                    @error('town')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">UC *</label>
                    <input type="text" name="uc" class="form-input" value="{{ old('uc') }}" required>
                    @error('uc')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Outreach *</label>
                    <input type="text" name="outreach" class="form-input" value="{{ old('outreach') }}" required>
                    @error('outreach')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <!-- Child Information -->
        <div style="margin-bottom: 32px;">
            <h3 class="form-section-title">Child Information</h3>
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">Child Name *</label>
                    <input type="text" name="child_name" class="form-input" value="{{ old('child_name') }}" required>
                    @error('child_name')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Father Name *</label>
                    <input type="text" name="father_name" class="form-input" value="{{ old('father_name') }}" required>
                    @error('father_name')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Gender *</label>
                    <select name="gender" class="form-input" required>
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Date of Birth *</label>
                    <input type="date" name="date_of_birth" class="form-input" value="{{ old('date_of_birth') }}" required>
                    @error('date_of_birth')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Age in Months *</label>
                    <input type="number" name="age_in_months" class="form-input" value="{{ old('age_in_months') }}" min="0" required>
                    @error('age_in_months')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Father CNIC</label>
                    <input type="text" name="father_cnic" class="form-input" value="{{ old('father_cnic') }}" placeholder="12345-1234567-1">
                    @error('father_cnic')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <!-- Contact & Address Information -->
        <div style="margin-bottom: 32px;">
            <h3 class="form-section-title">Contact & Address</h3>
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">House Number</label>
                    <input type="text" name="house_number" class="form-input" value="{{ old('house_number') }}">
                    @error('house_number')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Guardian Phone</label>
                    <input type="text" name="guardian_phone" class="form-input" value="{{ old('guardian_phone') }}" placeholder="03XXXXXXXXX">
                    @error('guardian_phone')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Address *</label>
                    <textarea name="address" class="form-input" rows="2" required>{{ old('address') }}</textarea>
                    @error('address')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <!-- Vaccination Information -->
        <div style="margin-bottom: 32px;">
            <h3 class="form-section-title">Vaccination Information</h3>
            <div>
                <div class="form-group">
                    <label class="form-label">Type *</label>
                    <select name="type" class="form-input" required>
                        <option value="">Select Type</option>
                        <option value="Zero Dose" {{ old('type') == 'Zero Dose' ? 'selected' : '' }}>Zero Dose</option>
                        <option value="Defaulter" {{ old('type') == 'Defaulter' ? 'selected' : '' }}>Defaulter</option>
                    </select>
                    @error('type')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Missed Vaccines * (Select multiple)</label>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-top: 8px;">
                        @php
                            $vaccines = ['BCG', 'OPV0', 'HepB', 'OPV1', 'Penta1', 'PCV1', 'OPV2', 'Penta2', 'PCV2', 'OPV3', 'Penta3', 'PCV3', 'IPV1', 'IPV2', 'Measles1', 'Measles2'];
                            $oldVaccines = old('missed_vaccines', []);
                        @endphp
                        @foreach($vaccines as $vaccine)
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="missed_vaccines[]" value="{{ $vaccine }}" {{ in_array($vaccine, $oldVaccines) ? 'checked' : '' }} style="width: 16px; height: 16px;">
                                <span>{{ $vaccine }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('missed_vaccines')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Reasons of Missing *</label>
                    <select name="reasons_of_missing" class="form-input" required>
                        <option value="">Select Reason</option>
                        <option value="Refusal" {{ old('reasons_of_missing') == 'Refusal' ? 'selected' : '' }}>Refusal</option>
                        <option value="Unaware" {{ old('reasons_of_missing') == 'Unaware' ? 'selected' : '' }}>Unaware</option>
                        <option value="Not Available" {{ old('reasons_of_missing') == 'Not Available' ? 'selected' : '' }}>Not Available</option>
                        <option value="Migration" {{ old('reasons_of_missing') == 'Migration' ? 'selected' : '' }}>Migration</option>
                        <option value="Other" {{ old('reasons_of_missing') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('reasons_of_missing')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Plan for Coverage *</label>
                    <textarea name="plan_for_coverage" class="form-input" rows="3" required>{{ old('plan_for_coverage') }}</textarea>
                    @error('plan_for_coverage')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <!-- Coordinates (Optional) -->
        <div style="margin-bottom: 32px;">
            <h3 class="form-section-title">Location Coordinates (Optional)</h3>
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">Latitude</label>
                    <input type="number" step="0.00000001" name="latitude" class="form-input" value="{{ old('latitude') }}" placeholder="24.8607">
                    @error('latitude')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Longitude</label>
                    <input type="number" step="0.00000001" name="longitude" class="form-input" value="{{ old('longitude') }}" placeholder="67.0011">
                    @error('longitude')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Entry</button>
            <a href="{{ route('admin.child-line-list.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
