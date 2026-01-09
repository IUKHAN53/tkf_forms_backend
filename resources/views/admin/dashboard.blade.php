@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="dashboard-grid">
    <!-- Core Forms Stats Cards -->
    <div class="stats-row">
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
                <h2>UC-wise Submissions</h2>
                <div class="chart-filters" id="uc-filters">
                    <select class="date-preset" data-chart="uc">
                        <option value="all" selected>All Time</option>
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="7days">Last 7 Days</option>
                        <option value="30days">Last 30 Days</option>
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="custom">Custom Range</option>
                    </select>
                    <div class="custom-date-range" style="display: none;">
                        <input type="date" class="start-date" placeholder="Start Date">
                        <input type="date" class="end-date" placeholder="End Date">
                        <button class="apply-filter btn btn-sm btn-primary" data-chart="uc">Apply</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="submissionsChart" height="80"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>District-wise Distribution</h2>
                <div class="chart-filters" id="district-filters">
                    <select class="date-preset" data-chart="district">
                        <option value="all" selected>All Time</option>
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="7days">Last 7 Days</option>
                        <option value="30days">Last 30 Days</option>
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="custom">Custom Range</option>
                    </select>
                    <div class="custom-date-range" style="display: none;">
                        <input type="date" class="start-date" placeholder="Start Date">
                        <input type="date" class="end-date" placeholder="End Date">
                        <button class="apply-filter btn btn-sm btn-primary" data-chart="district">Apply</button>
                    </div>
                </div>
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
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 16px;
}

.stat-card {
    background: white;
    border-radius: 14px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 1px 2px rgba(0, 0, 0, 0.02);
    padding: 20px 22px;
    display: flex;
    gap: 16px;
    align-items: center;
    border: 1px solid var(--gray-100);
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
}

.stat-card:nth-child(1)::before {
    background: linear-gradient(90deg, #22c55e, #4ade80);
}

.stat-card:nth-child(2)::before {
    background: linear-gradient(90deg, #f59e0b, #fbbf24);
}

.stat-card:nth-child(3)::before {
    background: linear-gradient(90deg, #ec4899, #f472b6);
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.stat-icon {
    width: 52px;
    height: 52px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.stat-content h3 {
    font-size: 28px;
    font-weight: 700;
    color: var(--gray-800);
    margin-bottom: 2px;
    letter-spacing: -0.5px;
    line-height: 1.2;
}

.stat-content p {
    color: var(--gray-500);
    font-size: 13px;
    font-weight: 500;
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

.card-header {
    flex-wrap: wrap;
    gap: var(--spacing-sm);
}

.chart-filters {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    flex-wrap: wrap;
}

.chart-filters .date-preset {
    padding: 6px 12px;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-sm);
    background: var(--color-bg-paper);
    color: var(--color-text-primary);
    font-size: 13px;
    cursor: pointer;
}

.chart-filters .custom-date-range {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.chart-filters .start-date,
.chart-filters .end-date {
    padding: 5px 10px;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-sm);
    background: var(--color-bg-paper);
    color: var(--color-text-primary);
    font-size: 13px;
}

.chart-filters .apply-filter {
    padding: 5px 12px;
    font-size: 13px;
}

@media (max-width: 768px) {
    .card-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .chart-filters {
        width: 100%;
    }

    .chart-filters .date-preset {
        flex: 1;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Store chart instances for updates
    let ucChart = null;
    let districtChart = null;

    // Chart configuration
    const chartOptions = {
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
    };

    // Dataset colors
    const datasetConfigs = [
        { label: 'FGDs-Community', key: 'fgds_community', color: 'rgba(34, 197, 94, 0.8)' },
        { label: 'FGDs-Health Workers', key: 'fgds_health_workers', color: 'rgba(245, 158, 11, 0.8)' },
        { label: 'Bridging The Gap', key: 'bridging_the_gap', color: 'rgba(236, 72, 153, 0.8)' }
    ];

    // Create chart with data
    function createChart(canvasId, data) {
        const labels = Object.keys(data);
        const datasets = datasetConfigs.map(config => ({
            label: config.label,
            data: labels.map(label => data[label][config.key] || 0),
            backgroundColor: config.color
        }));

        return new Chart(document.getElementById(canvasId), {
            type: 'bar',
            data: { labels, datasets },
            options: chartOptions
        });
    }

    // Update chart with new data
    function updateChart(chart, data) {
        const labels = Object.keys(data);
        chart.data.labels = labels;
        chart.data.datasets.forEach((dataset, index) => {
            const key = datasetConfigs[index].key;
            dataset.data = labels.map(label => data[label][key] || 0);
        });
        chart.update();
    }

    // Calculate date range from preset
    function getDateRange(preset) {
        const today = new Date();
        let startDate = null;
        let endDate = today.toISOString().split('T')[0];

        switch (preset) {
            case 'today':
                startDate = endDate;
                break;
            case 'yesterday':
                const yesterday = new Date(today);
                yesterday.setDate(yesterday.getDate() - 1);
                startDate = yesterday.toISOString().split('T')[0];
                endDate = startDate;
                break;
            case '7days':
                const week = new Date(today);
                week.setDate(week.getDate() - 6);
                startDate = week.toISOString().split('T')[0];
                break;
            case '30days':
                const month = new Date(today);
                month.setDate(month.getDate() - 29);
                startDate = month.toISOString().split('T')[0];
                break;
            case 'this_month':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                break;
            case 'last_month':
                const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                startDate = lastMonth.toISOString().split('T')[0];
                const lastDay = new Date(today.getFullYear(), today.getMonth(), 0);
                endDate = lastDay.toISOString().split('T')[0];
                break;
            case 'all':
            default:
                startDate = null;
                endDate = null;
                break;
        }

        return { startDate, endDate };
    }

    // Fetch chart data with filters
    async function fetchChartData(chartType, startDate, endDate) {
        const params = new URLSearchParams({ chart: chartType });
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

        try {
            const response = await fetch(`{{ route('admin.dashboard.chartData') }}?${params.toString()}`);
            const result = await response.json();
            if (result.success) {
                return result.data;
            }
        } catch (error) {
            console.error('Error fetching chart data:', error);
        }
        return null;
    }

    // Handle preset change
    async function handlePresetChange(select) {
        const chartType = select.dataset.chart;
        const preset = select.value;
        const filterContainer = select.closest('.chart-filters');
        const customRange = filterContainer.querySelector('.custom-date-range');

        if (preset === 'custom') {
            customRange.style.display = 'flex';
            return;
        }

        customRange.style.display = 'none';
        const { startDate, endDate } = getDateRange(preset);
        const data = await fetchChartData(chartType, startDate, endDate);

        if (data) {
            if (chartType === 'uc') {
                updateChart(ucChart, data);
            } else {
                updateChart(districtChart, data);
            }
        }
    }

    // Handle custom date apply
    async function handleCustomDateApply(button) {
        const chartType = button.dataset.chart;
        const filterContainer = button.closest('.chart-filters');
        const startDate = filterContainer.querySelector('.start-date').value;
        const endDate = filterContainer.querySelector('.end-date').value;

        if (!startDate || !endDate) {
            alert('Please select both start and end dates');
            return;
        }

        const data = await fetchChartData(chartType, startDate, endDate);

        if (data) {
            if (chartType === 'uc') {
                updateChart(ucChart, data);
            } else {
                updateChart(districtChart, data);
            }
        }
    }

    // Initialize UC Chart
    const ucData = @json($stats['uc_wise_submissions']);
    if (Object.keys(ucData).length > 0) {
        ucChart = createChart('submissionsChart', ucData);
    }

    // Initialize District Chart
    const districtData = @json($stats['district_distribution']);
    if (Object.keys(districtData).length > 0) {
        districtChart = createChart('districtChart', districtData);
    }

    // Event listeners for date presets
    document.querySelectorAll('.date-preset').forEach(select => {
        select.addEventListener('change', function() {
            handlePresetChange(this);
        });
    });

    // Event listeners for custom date apply buttons
    document.querySelectorAll('.apply-filter').forEach(button => {
        button.addEventListener('click', function() {
            handleCustomDateApply(this);
        });
    });
});
</script>
@endsection
