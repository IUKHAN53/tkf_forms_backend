@extends('layouts.admin')

@section('title', 'Vaccination Record Details')

@include('admin.core-forms.partials.styles')

@push('styles')
<style>
    .record-detail-container {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 24px;
    }
    @media (max-width: 1200px) {
        .record-detail-container {
            grid-template-columns: 1fr;
        }
    }
    .detail-section {
        background: white;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .detail-section-title {
        font-size: 15px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #10b981;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .detail-section-title svg {
        color: #10b981;
    }
    .detail-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }
    .detail-field {
        margin-bottom: 16px;
    }
    .detail-field label {
        display: block;
        font-size: 11px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }
    .detail-field .value {
        font-size: 15px;
        color: #1f2937;
        font-weight: 500;
        line-height: 1.5;
    }
    .detail-field .value.large {
        font-size: 18px;
        font-weight: 600;
        color: #111827;
    }
    .detail-field code {
        background: #ecfdf5;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 13px;
        font-family: 'SF Mono', Monaco, monospace;
        color: #047857;
    }
    .contact-with-call {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .call-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border-radius: 50%;
        text-decoration: none;
        transition: all 0.2s;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }
    .call-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    }
    .map-container {
        height: 350px;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        margin-top: 16px;
    }
    .map-container #recordMap {
        height: 100%;
        width: 100%;
    }
    .sidebar-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        margin-bottom: 20px;
    }
    .sidebar-card-title {
        font-size: 14px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .info-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .info-list-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 10px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    .info-list-item:last-child {
        border-bottom: none;
    }
    .info-list-item .label {
        font-size: 13px;
        color: #6b7280;
    }
    .info-list-item .value {
        font-size: 13px;
        font-weight: 600;
        color: #111827;
        text-align: right;
    }
    .status-indicator {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }
    .status-vaccinated {
        background: #d1fae5;
        color: #065f46;
    }
    .status-not-vaccinated {
        background: #fee2e2;
        color: #991b1b;
    }
    .category-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .category-defaulter {
        background: #dbeafe;
        color: #1e40af;
    }
    .category-refusal {
        background: #fee2e2;
        color: #991b1b;
    }
    .category-zero-dose {
        background: #fef3c7;
        color: #92400e;
    }
</style>
@endpush

@section('content')
<div class="content-card" style="background: transparent; box-shadow: none; border: none;">
    <div class="card-header" style="background: white; border-radius: 16px; margin-bottom: 24px;">
        <div class="header-left">
            <h2>Vaccination Record <code>{{ $vaccinationRecord->unique_id }}</code></h2>
            <p class="text-muted">Submitted on {{ $vaccinationRecord->created_at->format('M d, Y \a\t h:i A') }}</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.vaccination-records.index') }}" class="btn btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Back to List
            </a>
        </div>
    </div>

    <div class="record-detail-container">
        <!-- Main Content -->
        <div class="main-content">
            <!-- Child Information -->
            <div class="detail-section">
                <div class="detail-section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    Child Information
                </div>
                <div class="detail-row">
                    <div class="detail-field">
                        <label>Child Name</label>
                        <div class="value large">{{ $vaccinationRecord->child_name }}</div>
                    </div>
                    <div class="detail-field">
                        <label>Father Name</label>
                        <div class="value large">{{ $vaccinationRecord->father_name }}</div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-field">
                        <label>Age</label>
                        <div class="value">{{ $vaccinationRecord->age ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <label>Contact Number</label>
                        @if($vaccinationRecord->contact_number)
                        <div class="contact-with-call">
                            <span class="value">{{ $vaccinationRecord->contact_number }}</span>
                            <a href="tel:{{ $vaccinationRecord->contact_number }}" class="call-btn" title="Call">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                </svg>
                            </a>
                        </div>
                        @else
                        <div class="value">N/A</div>
                        @endif
                    </div>
                </div>
                <div class="detail-field">
                    <label>Address</label>
                    <div class="value">{{ $vaccinationRecord->address ?? 'N/A' }}</div>
                </div>
            </div>

            <!-- Vaccination Status -->
            <div class="detail-section">
                <div class="detail-section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                    </svg>
                    Vaccination Status
                </div>
                <div class="detail-row">
                    <div class="detail-field">
                        <label>Category</label>
                        @php
                            $catClass = match($vaccinationRecord->category) {
                                'Refusal' => 'category-refusal',
                                'Zero Dose' => 'category-zero-dose',
                                default => 'category-defaulter',
                            };
                        @endphp
                        <span class="category-badge {{ $catClass }}">{{ $vaccinationRecord->category }}</span>
                    </div>
                    <div class="detail-field">
                        <label>Vaccination Status</label>
                        <span class="status-indicator {{ $vaccinationRecord->vaccinated === 'YES' ? 'status-vaccinated' : 'status-not-vaccinated' }}">
                            @if($vaccinationRecord->vaccinated === 'YES')
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            Vaccinated
                            @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="15" y1="9" x2="9" y2="15"/>
                                <line x1="9" y1="9" x2="15" y2="15"/>
                            </svg>
                            Not Vaccinated
                            @endif
                        </span>
                    </div>
                    <div class="detail-field">
                        <label>Date of Vaccination</label>
                        <div class="value">{{ $vaccinationRecord->date_of_vaccination ? $vaccinationRecord->date_of_vaccination->format('M d, Y') : 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- Location Information with Map -->
            <div class="detail-section">
                <div class="detail-section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    Location Information
                </div>
                <div class="detail-row">
                    <div class="detail-field">
                        <label>District</label>
                        <div class="value">{{ $vaccinationRecord->district ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <label>UC (Union Council)</label>
                        <div class="value">{{ $vaccinationRecord->uc ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <label>Fix Site</label>
                        <div class="value">{{ $vaccinationRecord->fix_site ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-field">
                        <label>GPS Coordinates</label>
                        <div class="value">
                            @if($vaccinationRecord->latitude && $vaccinationRecord->longitude)
                            <code>{{ $vaccinationRecord->latitude }}, {{ $vaccinationRecord->longitude }}</code>
                            @else
                            N/A
                            @endif
                        </div>
                    </div>
                </div>

                @if($vaccinationRecord->latitude && $vaccinationRecord->longitude)
                <div class="map-container">
                    <div id="recordMap"></div>
                </div>
                @endif
            </div>

            <!-- Community Member -->
            <div class="detail-section">
                <div class="detail-section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    Community Member (Field Worker)
                </div>
                <div class="detail-row">
                    <div class="detail-field">
                        <label>Name</label>
                        <div class="value">{{ $vaccinationRecord->communityMember->name ?? $vaccinationRecord->community_member_name ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <label>Contact</label>
                        @php
                            $cmContact = $vaccinationRecord->communityMember->phone ?? $vaccinationRecord->community_member_contact ?? null;
                        @endphp
                        @if($cmContact)
                        <div class="contact-with-call">
                            <span class="value">{{ $cmContact }}</span>
                            <a href="tel:{{ $cmContact }}" class="call-btn" title="Call">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                </svg>
                            </a>
                        </div>
                        @else
                        <div class="value">N/A</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Submission Info Card -->
            <div class="sidebar-card">
                <div class="sidebar-card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    Submission Details
                </div>
                <div class="info-list">
                    <div class="info-list-item">
                        <span class="label">Form ID</span>
                        <span class="value"><code style="font-size: 11px;">{{ $vaccinationRecord->unique_id }}</code></span>
                    </div>
                    <div class="info-list-item">
                        <span class="label">Serial #</span>
                        <span class="value">{{ $vaccinationRecord->serial_number ?? 'N/A' }}</span>
                    </div>
                    <div class="info-list-item">
                        <span class="label">Submitted By</span>
                        <span class="value">{{ $vaccinationRecord->communityMember->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-list-item">
                        <span class="label">Started At</span>
                        <span class="value">{{ $vaccinationRecord->started_at ? $vaccinationRecord->started_at->format('M d, Y h:i A') : 'N/A' }}</span>
                    </div>
                    <div class="info-list-item">
                        <span class="label">Submitted At</span>
                        <span class="value">{{ $vaccinationRecord->submitted_at ? $vaccinationRecord->submitted_at->format('M d, Y h:i A') : 'N/A' }}</span>
                    </div>
                    <div class="info-list-item">
                        <span class="label">IP Address</span>
                        <span class="value">{{ $vaccinationRecord->ip_address ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Device Info Card -->
            @if($vaccinationRecord->device_info)
            <div class="sidebar-card">
                <div class="sidebar-card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
                        <line x1="12" y1="18" x2="12.01" y2="18"/>
                    </svg>
                    Device Information
                </div>
                <div class="info-list">
                    <div class="info-list-item">
                        <span class="label">Platform</span>
                        <span class="value">{{ $vaccinationRecord->device_info['platform'] ?? 'N/A' }}</span>
                    </div>
                    <div class="info-list-item">
                        <span class="label">OS Version</span>
                        <span class="value">{{ $vaccinationRecord->device_info['os_version'] ?? 'N/A' }}</span>
                    </div>
                    <div class="info-list-item">
                        <span class="label">Device</span>
                        <span class="value">{{ ($vaccinationRecord->device_info['device_brand'] ?? '') . ' ' . ($vaccinationRecord->device_info['device_model'] ?? '') }}</span>
                    </div>
                    <div class="info-list-item">
                        <span class="label">App Version</span>
                        <span class="value">v{{ $vaccinationRecord->device_info['app_version'] ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@if($vaccinationRecord->latitude && $vaccinationRecord->longitude)
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const lat = {{ $vaccinationRecord->latitude }};
    const lng = {{ $vaccinationRecord->longitude }};

    const map = L.map('recordMap').setView([lat, lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const marker = L.marker([lat, lng]).addTo(map);
    marker.bindPopup(`
        <strong>{{ $vaccinationRecord->child_name }}</strong><br>
        Father: {{ $vaccinationRecord->father_name }}<br>
        Category: {{ $vaccinationRecord->category }}<br>
        Status: {{ $vaccinationRecord->vaccinated === 'YES' ? 'Vaccinated' : 'Not Vaccinated' }}
    `).openPopup();
});
</script>
@endif
@endsection
