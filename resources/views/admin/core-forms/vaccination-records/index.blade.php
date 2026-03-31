@extends('layouts.admin')

@section('title', 'Vaccination Records')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <h2>Vaccination Records</h2>
            <p class="text-muted">CLM Tracker - Track defaulters, refusals, and vaccination status</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.vaccination-records.template') }}" class="btn btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Download Template
            </a>
            <a href="{{ route('admin.vaccination-records.export') }}" class="btn btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                Export CSV
            </a>
            <button type="button" class="btn btn-primary" onclick="document.getElementById('importModal').showModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Import CSV
            </button>
        </div>
    </div>

    <!-- Stats Summary -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; padding: 20px 24px;">
        <div style="background: #EFF6FF; border-radius: 12px; padding: 16px; text-align: center;">
            <div style="font-size: 24px; font-weight: 700; color: #1E40AF;">{{ $stats['total'] }}</div>
            <div style="font-size: 12px; color: #6B7280; margin-top: 4px;">Total Records</div>
        </div>
        <div style="background: #D1FAE5; border-radius: 12px; padding: 16px; text-align: center;">
            <div style="font-size: 24px; font-weight: 700; color: #065F46;">{{ $stats['vaccinated'] }}</div>
            <div style="font-size: 12px; color: #6B7280; margin-top: 4px;">Vaccinated</div>
        </div>
        <div style="background: #FEE2E2; border-radius: 12px; padding: 16px; text-align: center;">
            <div style="font-size: 24px; font-weight: 700; color: #991B1B;">{{ $stats['refusals'] }}</div>
            <div style="font-size: 12px; color: #6B7280; margin-top: 4px;">Pending Refusals</div>
        </div>
        <div style="background: #FEF3C7; border-radius: 12px; padding: 16px; text-align: center;">
            <div style="font-size: 24px; font-weight: 700; color: #92400E;">{{ $stats['zero_dose'] }}</div>
            <div style="font-size: 12px; color: #6B7280; margin-top: 4px;">Zero Dose</div>
        </div>
    </div>

    @include('admin.core-forms.partials.map', ['mapData' => $mapData])

    <div class="card-filters">
        <form action="{{ route('admin.vaccination-records.index') }}" method="GET" class="search-form">
            <input type="text" name="search" class="form-input" placeholder="Search by name, UC, or district..." value="{{ request('search') }}">
            <select name="category" class="form-input" style="width: auto;">
                <option value="">All Categories</option>
                <option value="Defaulter" {{ request('category') == 'Defaulter' ? 'selected' : '' }}>Defaulter</option>
                <option value="Refusal" {{ request('category') == 'Refusal' ? 'selected' : '' }}>Refusal</option>
                <option value="Zero Dose" {{ request('category') == 'Zero Dose' ? 'selected' : '' }}>Zero Dose</option>
            </select>
            <select name="vaccinated" class="form-input" style="width: auto;">
                <option value="">All Status</option>
                <option value="YES" {{ request('vaccinated') == 'YES' ? 'selected' : '' }}>Vaccinated</option>
                <option value="NO" {{ request('vaccinated') == 'NO' ? 'selected' : '' }}>Not Vaccinated</option>
            </select>
            <button type="submit" class="btn btn-primary">Search</button>
            @if(request('search') || request('category') || request('vaccinated'))
                <a href="{{ route('admin.vaccination-records.index') }}" class="btn btn-outline">Clear</a>
            @endif
        </form>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Form ID</th>
                    <th>Child Name</th>
                    <th>Father Name</th>
                    <th>Age</th>
                    <th>Category</th>
                    <th>Vaccinated</th>
                    <th>District</th>
                    <th>UC</th>
                    <th>Submitted By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $item)
                    <tr>
                        <td><code>{{ $item->unique_id }}</code></td>
                        <td>{{ $item->child_name }}</td>
                        <td>{{ $item->father_name }}</td>
                        <td>{{ $item->age }}</td>
                        <td>
                            @php
                                $catClass = match($item->category) {
                                    'Refusal' => 'badge-danger',
                                    'Zero Dose' => 'badge-warning',
                                    default => 'badge-info',
                                };
                            @endphp
                            <span class="badge {{ $catClass }}">{{ $item->category }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $item->vaccinated === 'YES' ? 'badge-success' : 'badge-danger' }}">
                                {{ $item->vaccinated }}
                            </span>
                        </td>
                        <td>{{ $item->district ?? '-' }}</td>
                        <td>{{ $item->uc ?? '-' }}</td>
                        <td>{{ $item->communityMember->name ?? $item->community_member_name ?? 'N/A' }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.vaccination-records.show', $item) }}" class="btn-icon btn-icon-view" title="View Details">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.vaccination-records.edit', $item) }}" class="btn-icon btn-icon-edit" title="Edit Record">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.vaccination-records.destroy', $item) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this vaccination record? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon btn-icon-delete" title="Delete Record">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            <line x1="10" y1="11" x2="10" y2="17"/>
                                            <line x1="14" y1="11" x2="14" y2="17"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted">No vaccination records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer">
        {{ $records->links() }}
    </div>
</div>

<!-- Import Modal -->
<dialog id="importModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Import Vaccination Records</h3>
            <button type="button" class="modal-close" onclick="document.getElementById('importModal').close()">&times;</button>
        </div>
        <form action="{{ route('admin.vaccination-records.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <p class="mb-md text-muted">Upload a CSV file to import vaccination records. Download the template first to see the required format.</p>
                <input type="file" name="file" accept=".csv" required class="form-input" style="width: 100%;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('importModal').close()">Cancel</button>
                <button type="submit" class="btn btn-primary">Import</button>
            </div>
        </form>
    </div>
</dialog>
@endsection
