@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="dashboard-grid">
    <!-- Core Forms Stats Cards -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path d="M12 2a3 3 0 0 0-3 3v1a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3z"></path>
                    <path d="M19 8a2 2 0 0 1 2 2v1a2 2 0 0 1-4 0v-1a2 2 0 0 1 2-2z"></path>
                    <path d="M5 8a2 2 0 0 1 2 2v1a2 2 0 0 1-4 0v-1a2 2 0 0 1 2-2z"></path>
                    <path d="M12 14a4 4 0 0 0-4 4v4h8v-4a4 4 0 0 0-4-4z"></path>
                    <path d="M5 14a3 3 0 0 0-3 3v5h6v-5a3 3 0 0 0-3-3z"></path>
                    <path d="M19 14a3 3 0 0 1 3 3v5h-6v-5a3 3 0 0 1 3-3z"></path>
                </svg>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['core_forms']['child_line_lists'] }}</h3>
                <p>Children Registered</p>
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

    <!-- UC Stats Cards Section -->
    <div class="section-header">
        <h2>Union Council Statistics</h2>
        <p class="text-muted">Click on any UC card to view detailed statistics and submissions</p>
    </div>
    <div class="uc-cards-grid">
        @foreach($stats['uc_stats'] as $index => $uc)
        <a href="{{ route('admin.uc.show', $uc['slug']) }}" class="uc-card" data-index="{{ $index }}">
            <div class="uc-card-header">
                <div class="uc-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                </div>
                <div class="uc-card-title">
                    <h3>{{ $uc['name'] }}</h3>
                    <span class="uc-total-badge">{{ $uc['total'] }} Total</span>
                </div>
            </div>
            <div class="uc-card-stats">
                <div class="uc-stat-item">
                    <span class="uc-stat-dot" style="background: #6366f1;"></span>
                    <span class="uc-stat-label">Children</span>
                    <span class="uc-stat-value">{{ $uc['child_line_lists'] }}</span>
                </div>
                <div class="uc-stat-item">
                    <span class="uc-stat-dot" style="background: #22c55e;"></span>
                    <span class="uc-stat-label">FGDs-Community</span>
                    <span class="uc-stat-value">{{ $uc['fgds_community'] }}</span>
                </div>
                <div class="uc-stat-item">
                    <span class="uc-stat-dot" style="background: #f59e0b;"></span>
                    <span class="uc-stat-label">FGDs-Health</span>
                    <span class="uc-stat-value">{{ $uc['fgds_health_workers'] }}</span>
                </div>
                <div class="uc-stat-item">
                    <span class="uc-stat-dot" style="background: #ec4899;"></span>
                    <span class="uc-stat-label">Bridging Gap</span>
                    <span class="uc-stat-value">{{ $uc['bridging_the_gap'] }}</span>
                </div>
            </div>
            <div class="uc-card-footer">
                <span class="view-details">View Details</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </div>
        </a>
        @endforeach
    </div>

    <!-- District Chart -->
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

    <!-- Recent Activity -->
    <div class="card">
        <div class="card-header">
            <h2>Recent Activity</h2>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Description</th>
                        <th>District</th>
                        <th>UC</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stats['recent_activity'] as $activity)
                        <tr>
                            <td><span class="badge badge-{{ $activity['type'] === 'Child Line List' ? 'info' : ($activity['type'] === 'FGDs-Community' ? 'success' : ($activity['type'] === 'FGDs-Health Workers' ? 'warning' : 'primary')) }}">{{ $activity['type'] }}</span></td>
                            <td>{{ \Illuminate\Support\Str::limit($activity['description'], 40) }}</td>
                            <td>{{ $activity['district'] ?? 'N/A' }}</td>
                            <td>{{ $activity['uc'] ?? 'N/A' }}</td>
                            <td>{{ $activity['created_at']->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary">No recent activity</td>
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

.section-header {
    margin-bottom: -8px;
}

.section-header h2 {
    font-size: 18px;
    font-weight: 700;
    color: var(--gray-800);
    margin-bottom: 4px;
}

.section-header .text-muted {
    font-size: 13px;
    color: var(--gray-500);
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
    background: linear-gradient(90deg, #6366f1, #818cf8);
}

.stat-card:nth-child(2)::before {
    background: linear-gradient(90deg, #22c55e, #4ade80);
}

.stat-card:nth-child(3)::before {
    background: linear-gradient(90deg, #f59e0b, #fbbf24);
}

.stat-card:nth-child(4)::before {
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

/* UC Cards Grid */
.uc-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
}

.uc-card {
    background: white;
    border-radius: 14px;
    border: 1px solid var(--gray-200);
    padding: 18px;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
}

.uc-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--uc-color-1, #6366f1), var(--uc-color-2, #818cf8));
}

.uc-card[data-index="0"]::before { --uc-color-1: #6366f1; --uc-color-2: #818cf8; }
.uc-card[data-index="1"]::before { --uc-color-1: #22c55e; --uc-color-2: #4ade80; }
.uc-card[data-index="2"]::before { --uc-color-1: #f59e0b; --uc-color-2: #fbbf24; }
.uc-card[data-index="3"]::before { --uc-color-1: #ec4899; --uc-color-2: #f472b6; }
.uc-card[data-index="4"]::before { --uc-color-1: #06b6d4; --uc-color-2: #22d3ee; }
.uc-card[data-index="5"]::before { --uc-color-1: #8b5cf6; --uc-color-2: #a78bfa; }
.uc-card[data-index="6"]::before { --uc-color-1: #ef4444; --uc-color-2: #f87171; }
.uc-card[data-index="7"]::before { --uc-color-1: #14b8a6; --uc-color-2: #2dd4bf; }
.uc-card[data-index="8"]::before { --uc-color-1: #3b82f6; --uc-color-2: #60a5fa; }

.uc-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    border-color: var(--gray-300);
}

.uc-card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}

.uc-card-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: var(--gray-100);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.uc-card-icon svg {
    width: 22px;
    height: 22px;
    color: var(--gray-600);
}

.uc-card-title h3 {
    font-size: 16px;
    font-weight: 700;
    color: var(--gray-800);
    margin: 0 0 2px 0;
    line-height: 1.3;
}

.uc-total-badge {
    font-size: 12px;
    font-weight: 600;
    color: var(--gray-500);
    background: var(--gray-100);
    padding: 2px 8px;
    border-radius: 10px;
}

.uc-card-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    margin-bottom: 14px;
}

.uc-stat-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
}

.uc-stat-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}

.uc-stat-label {
    color: var(--gray-500);
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.uc-stat-value {
    font-weight: 700;
    color: var(--gray-700);
}

.uc-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 12px;
    border-top: 1px solid var(--gray-100);
    margin-top: auto;
}

.view-details {
    font-size: 13px;
    font-weight: 600;
    color: var(--primary-600);
}

.uc-card-footer svg {
    color: var(--primary-600);
    transition: transform 0.2s ease;
}

.uc-card:hover .uc-card-footer svg {
    transform: translateX(3px);
}

/* Card styles */
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-lg);
    flex-wrap: wrap;
    gap: var(--spacing-sm);
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

.badge-success { background: #dcfce7; color: #166534; }
.badge-warning { background: #fef3c7; color: #92400e; }
.badge-primary { background: #fce7f3; color: #9d174d; }

@media (max-width: 768px) {
    .uc-cards-grid {
        grid-template-columns: 1fr;
    }

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
    // Store chart instance for updates
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
        { label: 'Child Line Lists', key: 'child_line_lists', color: 'rgba(99, 102, 241, 0.8)' },
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
            updateChart(districtChart, data);
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
            updateChart(districtChart, data);
        }
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
