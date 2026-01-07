@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="dashboard-grid">
    <!-- Core Forms Stats Cards -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                </svg>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['core_forms']['child_line_lists'] }}</h3>
                <p>Child Line Lists</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['core_forms']['fgds_community'] }}</h3>
                <p>FGDs-Community</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                </svg>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['core_forms']['fgds_health_workers'] }}</h3>
                <p>FGDs-Health Workers</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
                    <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
                    <line x1="6" y1="1" x2="6" y2="4"></line>
                    <line x1="10" y1="1" x2="10" y2="4"></line>
                    <line x1="14" y1="1" x2="14" y2="4"></line>
                </svg>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['core_forms']['bridging_the_gap'] }}</h3>
                <p>Bridging The Gap</p>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="charts-row">
        <div class="card">
            <div class="card-header">
                <h2>UC-wise Submissions (Top 15)</h2>
            </div>
            <div class="card-body">
                <canvas id="submissionsChart" height="80"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>District-wise Distribution</h2>
            </div>
            <div class="card-body">
                <canvas id="districtChart" height="80"></canvas>
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

.charts-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: var(--spacing-lg);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // UC-wise Submissions Chart (Stacked Bar)
    const ucData = @json($stats['uc_wise_submissions']);
    const ucs = Object.keys(ucData);

    if (ucs.length > 0) {
        const childLineListData = ucs.map(uc => ucData[uc].child_line_lists || 0);
        const fgdsCommunityData = ucs.map(uc => ucData[uc].fgds_community || 0);
        const fgdsHealthWorkersData = ucs.map(uc => ucData[uc].fgds_health_workers || 0);
        const bridgingTheGapData = ucs.map(uc => ucData[uc].bridging_the_gap || 0);

        new Chart(document.getElementById('submissionsChart'), {
            type: 'bar',
            data: {
                labels: ucs,
                datasets: [
                    {
                        label: 'Child Line Lists',
                        data: childLineListData,
                        backgroundColor: 'rgba(99, 102, 241, 0.8)',
                    },
                    {
                        label: 'FGDs-Community',
                        data: fgdsCommunityData,
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    },
                    {
                        label: 'FGDs-Health Workers',
                        data: fgdsHealthWorkersData,
                        backgroundColor: 'rgba(245, 158, 11, 0.8)',
                    },
                    {
                        label: 'Bridging The Gap',
                        data: bridgingTheGapData,
                        backgroundColor: 'rgba(236, 72, 153, 0.8)',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // District Distribution Chart (Stacked Bar)
    const districtData = @json($stats['district_distribution']);
    const districts = Object.keys(districtData);

    if (districts.length > 0) {
        const districtChildLineLists = districts.map(d => districtData[d].child_line_lists || 0);
        const districtFgdsCommunity = districts.map(d => districtData[d].fgds_community || 0);
        const districtFgdsHealthWorkers = districts.map(d => districtData[d].fgds_health_workers || 0);
        const districtBridgingTheGap = districts.map(d => districtData[d].bridging_the_gap || 0);

        new Chart(document.getElementById('districtChart'), {
            type: 'bar',
            data: {
                labels: districts,
                datasets: [
                    {
                        label: 'Child Line Lists',
                        data: districtChildLineLists,
                        backgroundColor: 'rgba(99, 102, 241, 0.8)',
                    },
                    {
                        label: 'FGDs-Community',
                        data: districtFgdsCommunity,
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    },
                    {
                        label: 'FGDs-Health Workers',
                        data: districtFgdsHealthWorkers,
                        backgroundColor: 'rgba(245, 158, 11, 0.8)',
                    },
                    {
                        label: 'Bridging The Gap',
                        data: districtBridgingTheGap,
                        backgroundColor: 'rgba(236, 72, 153, 0.8)',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection
