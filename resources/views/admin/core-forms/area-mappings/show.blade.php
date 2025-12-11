@extends('layouts.admin')

@section('title', 'Area Mapping Details')
@section('page-title', 'Area Mapping Details')

@include('admin.core-forms.partials.styles')

@section('content')
<div class="content-card">
    <div class="card-header">
        <div class="header-left">
            <a href="{{ route('admin.area-mappings.index') }}" class="btn btn-outline btn-sm">← Back to List</a>
            <h2>Area Mapping #{{ $areaMapping->id }}</h2>
        </div>
    </div>

    <div class="detail-grid">
        <div class="detail-item">
            <label>District</label>
            <span>{{ $areaMapping->district }}</span>
        </div>
        <div class="detail-item">
            <label>UC Name</label>
            <span>{{ $areaMapping->uc_name }}</span>
        </div>
        <div class="detail-item">
            <label>Tehsil</label>
            <span>{{ $areaMapping->tehsil }}</span>
        </div>
        <div class="detail-item">
            <label>Area Name</label>
            <span>{{ $areaMapping->area_name }}</span>
        </div>
        <div class="detail-item">
            <label>Total Households</label>
            <span>{{ $areaMapping->total_households }}</span>
        </div>
        <div class="detail-item">
            <label>Total Children</label>
            <span>{{ $areaMapping->total_children }}</span>
        </div>
        <div class="detail-item">
            <label>Latitude</label>
            <span>{{ $areaMapping->latitude ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Longitude</label>
            <span>{{ $areaMapping->longitude ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <label>Created At</label>
            <span>{{ $areaMapping->created_at->format('M d, Y h:i A') }}</span>
        </div>
        <div class="detail-item">
            <label>Updated At</label>
            <span>{{ $areaMapping->updated_at->format('M d, Y h:i A') }}</span>
        </div>
    </div>
</div>

@include('admin.core-forms.partials.styles')
@endsection
