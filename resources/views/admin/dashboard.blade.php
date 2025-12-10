@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="dashboard-grid">
    <!-- Stats Cards -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total_forms'] }}</h3>
                <p>Total Forms</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-success">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <polyline points="9 11 12 14 22 4"></polyline>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                </svg>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['active_forms'] }}</h3>
                <p>Active Forms</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-info">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                </svg>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total_submissions'] }}</h3>
                <p>Total Submissions</p>
            </div>
        </div>
    </div>

    <!-- Recent Submissions -->
    <div class="card">
        <div class="card-header">
            <h2>Recent Submissions</h2>
            <a href="{{ route('admin.submissions.index') }}" class="btn btn-sm btn-secondary">View All</a>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Form</th>
                        <th>Submitted By</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stats['recent_submissions'] as $submission)
                        <tr>
                            <td><span class="badge badge-info">#{{ $submission->id }}</span></td>
                            <td>{{ $submission->form->name }}</td>
                            <td>{{ $submission->user->name ?? 'Anonymous' }}</td>
                            <td>{{ $submission->created_at->diffForHumans() }}</td>
                            <td>
                                <a href="{{ route('admin.submissions.show', $submission) }}" class="btn btn-sm btn-secondary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary">No submissions yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.dashboard-grid {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-lg);
}

.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: var(--spacing-lg);
}

.stat-card {
    background: var(--color-bg-paper);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-card);
    padding: var(--spacing-lg);
    display: flex;
    gap: var(--spacing-md);
    align-items: center;
}

.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-content h3 {
    font-size: 28px;
    font-weight: 700;
    color: var(--color-text-primary);
    margin-bottom: 4px;
}

.stat-content p {
    color: var(--color-text-secondary);
    font-size: 14px;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-lg);
}

.card-header h2 {
    font-size: 18px;
    font-weight: 700;
    color: var(--color-text-primary);
}

.table-wrapper {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    text-align: left;
    padding: 12px;
    font-weight: 600;
    color: var(--color-text-secondary);
    font-size: 14px;
    border-bottom: 2px solid var(--color-border);
}

.data-table td {
    padding: 12px;
    border-bottom: 1px solid var(--color-border);
    color: var(--color-text-primary);
}

.data-table tr:last-child td {
    border-bottom: none;
}

.text-center {
    text-align: center;
}
</style>
@endsection
