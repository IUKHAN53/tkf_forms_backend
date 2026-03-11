@extends('layouts.admin')

@section('title', 'Community Members')
@section('page-title', 'Community Members')

@section('content')
<div class="page-header">
    <div class="page-header-actions">
        <a href="{{ route('admin.community-members.create') }}" class="btn btn-primary">Add Member</a>
    </div>
</div>

<div class="card">
    <form method="GET" action="{{ route('admin.community-members.index') }}" style="padding: 16px 20px; display: flex; gap: 12px; flex-wrap: wrap; align-items: center; border-bottom: 1px solid var(--neutral-200);">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, phone, district..." class="form-input" style="max-width: 280px;">
        <select name="district" class="form-input" style="max-width: 200px;">
            <option value="">All Districts</option>
            @foreach($districts as $d)
                <option value="{{ $d }}" {{ request('district') === $d ? 'selected' : '' }}>{{ $d }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        @if(request()->hasAny(['search', 'district']))
            <a href="{{ route('admin.community-members.index') }}" class="btn btn-outline btn-sm">Clear</a>
        @endif
    </form>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>District</th>
                    <th>UC</th>
                    <th>Fix Site</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $member)
                    <tr>
                        <td><span class="badge badge-info">#{{ $member->id }}</span></td>
                        <td>{{ $member->name }}</td>
                        <td>{{ $member->phone }}</td>
                        <td>{{ $member->district ?? '-' }}</td>
                        <td>{{ $member->uc ?? '-' }}</td>
                        <td>{{ $member->fix_site ?? '-' }}</td>
                        <td>
                            @if($member->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $member->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.community-members.edit', $member) }}" class="btn btn-sm btn-secondary">Edit</a>
                                <form action="{{ route('admin.community-members.destroy', $member) }}" method="POST" onsubmit="return confirm('Delete this community member?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-secondary">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-secondary">No community members found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="pagination-wrapper">
    {{ $members->links() }}
</div>
@endsection
