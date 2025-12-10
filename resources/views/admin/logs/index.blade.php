@extends('layouts.admin')

@section('title', 'Activity Logs')
@section('page-title', 'Activity Logs')

@section('content')
<div class="card">
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>IP</th>
                    <th>User Agent</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>{{ $log->user?->email ?? 'System' }}</td>
                        <td><span class="badge badge-info">{{ $log->action }}</span></td>
                        <td>{{ $log->description }}</td>
                        <td>{{ $log->ip_address }}</td>
                        <td style="max-width: 240px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $log->user_agent }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-secondary">No logs</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="pagination-wrapper">
    {{ $logs->links() }}
</div>
@endsection
