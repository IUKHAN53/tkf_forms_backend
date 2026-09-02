<script>
document.addEventListener('DOMContentLoaded', function() {
    const ucSlug = '{{ $ucSlug }}';
    let currentTab = 'fgds_community';
    let startDate = null;
    let endDate = null;
    let subsetUc = 'all';

    // Elements
    const datePreset = document.getElementById('datePreset');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const applyBtn = document.getElementById('applyFilters');
    const subsetUcSelect = document.getElementById('subsetUc');
    const customDateElements = document.querySelectorAll('.custom-date-range');
    const loadingState = document.getElementById('loadingState');
    const tabStats = document.getElementById('tabStats');
    const tableHead = document.getElementById('tableHead');
    const tableBody = document.getElementById('tableBody');
    const tableTitle = document.getElementById('tableTitle');
    const recordCount = document.getElementById('recordCount');

    // Tab buttons
    const tabBtns = document.querySelectorAll('.tab-btn');

    // Map initialization - always show map
    const mapData = @json($mapData);
    const map = L.map('ucMap').setView([24.8607, 67.0011], 11);

    L.tileLayer('{{ config('services.carto.basemap_url') }}', {
        attribution: '&copy; OpenStreetMap &copy; CARTO',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);

    // Marker layers by type
    const layers = {
        fgds_community: L.layerGroup(),
        fgds_health_workers: L.layerGroup(),
        bridging_the_gap: L.layerGroup()
    };

    const markerColors = {
        fgds_community: '#22c55e',
        fgds_health_workers: '#f59e0b',
        bridging_the_gap: '#ec4899'
    };

    // Create markers if we have data
    if (mapData.length > 0) {
        mapData.forEach(loc => {
            const color = markerColors[loc.type] || '#6366f1';
            const icon = L.divIcon({
                className: 'custom-marker',
                html: `<div style="width:12px;height:12px;background:${color};border:2px solid white;border-radius:50%;box-shadow:0 2px 6px rgba(0,0,0,0.3);"></div>`,
                iconSize: [12, 12],
                iconAnchor: [6, 6]
            });

            const marker = L.marker([loc.lat, loc.lon], { icon: icon });
            marker.bindPopup(loc.popup);
            marker.ucVariant = loc.uc;
            layers[loc.type].addLayer(marker);
        });

        // Add all layers to map
        Object.values(layers).forEach(layer => layer.addTo(map));

        // Fit bounds
        const allMarkers = [];
        mapData.forEach(loc => allMarkers.push([loc.lat, loc.lon]));
        if (allMarkers.length > 0) {
            map.fitBounds(allMarkers, { padding: [30, 30] });
        }
    }

    // Toggle layer buttons
    document.querySelectorAll('.map-btn[data-type]').forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.dataset.type;
            if (this.classList.contains('active')) {
                map.removeLayer(layers[type]);
                this.classList.remove('active');
            } else {
                layers[type].addTo(map);
                this.classList.add('active');
            }
        });
    });

    // Filter markers by subset UC
    function filterMapBySubset(selectedUc) {
        Object.entries(layers).forEach(([type, layer]) => {
            layer.eachLayer(marker => {
                if (selectedUc === 'all' || marker.ucVariant === selectedUc) {
                    marker.setOpacity(1);
                } else {
                    marker.setOpacity(0.2);
                }
            });
        });
    }

    // Tab click handler
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentTab = this.dataset.tab;
            loadData();
        });
    });

    // Subset UC change
    if (subsetUcSelect) {
        subsetUcSelect.addEventListener('change', function() {
            subsetUc = this.value;
            if (mapData.length > 0) {
                filterMapBySubset(subsetUc);
            }
            loadData();
        });
    }

    // Date preset change
    datePreset.addEventListener('change', function() {
        if (this.value === 'custom') {
            customDateElements.forEach(el => el.style.display = 'flex');
        } else {
            customDateElements.forEach(el => el.style.display = 'none');
            const range = getDateRange(this.value);
            startDate = range.startDate;
            endDate = range.endDate;
            loadData();
        }
    });

    // Apply filters button
    applyBtn.addEventListener('click', function() {
        startDate = startDateInput.value || null;
        endDate = endDateInput.value || null;
        loadData();
    });

    // Calculate date range
    function getDateRange(preset) {
        const today = new Date();
        let start = null;
        let end = today.toISOString().split('T')[0];

        switch (preset) {
            case 'today':
                start = end;
                break;
            case 'yesterday':
                const yesterday = new Date(today);
                yesterday.setDate(yesterday.getDate() - 1);
                start = yesterday.toISOString().split('T')[0];
                end = start;
                break;
            case '7days':
                const week = new Date(today);
                week.setDate(week.getDate() - 6);
                start = week.toISOString().split('T')[0];
                break;
            case '30days':
                const month = new Date(today);
                month.setDate(month.getDate() - 29);
                start = month.toISOString().split('T')[0];
                break;
            case 'this_month':
                start = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                break;
            case 'last_month':
                const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                start = lastMonth.toISOString().split('T')[0];
                const lastDay = new Date(today.getFullYear(), today.getMonth(), 0);
                end = lastDay.toISOString().split('T')[0];
                break;
            case 'all':
            default:
                start = null;
                end = null;
                break;
        }

        return { startDate: start, endDate: end };
    }

    // Load data via AJAX
    async function loadData() {
        loadingState.classList.add('active');
        tabStats.innerHTML = '';
        tableHead.innerHTML = '';
        tableBody.innerHTML = '';
        document.getElementById('barriersCategorySection').style.display = 'none';

        const params = new URLSearchParams({ tab: currentTab });
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        if (subsetUc && subsetUc !== 'all') params.append('subset_uc', subsetUc);

        try {
            const response = await fetch(`{{ route('admin.uc.data', $ucSlug) }}?${params.toString()}`);
            const result = await response.json();

            if (result.success) {
                renderStats(result.data.stats);
                renderBarriersByCategory(result.data.barriers_by_category);
                renderTable(result.data.records);
            }
        } catch (error) {
            console.error('Error loading data:', error);
            tableBody.innerHTML = '<tr><td colspan="10" class="empty-state"><p>Error loading data</p></td></tr>';
        } finally {
            loadingState.classList.remove('active');
        }
    }

    // Render barriers by category grid
    function renderBarriersByCategory(categories) {
        const section = document.getElementById('barriersCategorySection');
        const grid = document.getElementById('barriersCategoryGrid');
        if (!categories || !categories.length) {
            section.style.display = 'none';
            return;
        }
        const totalBarriers = categories.reduce((sum, c) => sum + c.count, 0);
        if (totalBarriers === 0) {
            section.style.display = 'none';
            return;
        }
        const categoryColors = [
            { bg: '#fef2f2', border: '#fecaca', text: '#991b1b' },
            { bg: '#fff7ed', border: '#fed7aa', text: '#9a3412' },
            { bg: '#fffbeb', border: '#fde68a', text: '#92400e' },
            { bg: '#f0fdf4', border: '#bbf7d0', text: '#166534' },
            { bg: '#eff6ff', border: '#bfdbfe', text: '#1e40af' },
            { bg: '#eef2ff', border: '#c7d2fe', text: '#3730a3' },
            { bg: '#faf5ff', border: '#e9d5ff', text: '#6b21a8' },
            { bg: '#fdf2f8', border: '#fbcfe8', text: '#9d174d' },
        ];
        let html = '';
        categories.forEach((cat, i) => {
            const color = categoryColors[i % categoryColors.length];
            // Always clickable, like the FGDs list cards: clicking opens the modal,
            // which shows a "no FGDs" message when the category has zero barriers.
            const cursor = 'pointer';
            // Encode the quotes JSON.stringify adds so they don't terminate the
            // onclick="" attribute early — that bug stopped the UC modal from ever
            // opening (the FGDs list avoids it via Blade's js directive).
            const nameArg = JSON.stringify(cat.name).replace(/"/g, '&quot;');
            const onclick = ` onclick="openBarrierCategoryModal(${cat.id}, ${nameArg})"`;
            const hint = `<div style="font-size: 10px; color: ${color.text}; margin-top: 6px; opacity: 0.7;">Click to view FGDs</div>`;
            html += `<div class="barrier-cat-card"${onclick} style="background: ${color.bg}; border: 1px solid ${color.border}; border-radius: 10px; padding: 14px 16px; text-align: center; cursor: ${cursor};">
                <div style="font-size: 22px; font-weight: 700; color: ${color.text}; line-height: 1.2;">${cat.count}</div>
                <div style="font-size: 12px; color: ${color.text}; margin-top: 4px; opacity: 0.85; line-height: 1.3;">${cat.name}</div>
                ${hint}
            </div>`;
        });
        grid.innerHTML = html;
        section.style.display = 'block';
    }

    // ----- Barrier category drill-down modal -----
    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, s => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[s]));
    }

    // Exposed on window because the category cards call it from an inline
    // onclick, which runs in global scope (this script is inside DOMContentLoaded).
    window.openBarrierCategoryModal = async function (categoryId, categoryName) {
        const overlay = document.getElementById('barrierModalOverlay');
        const body = document.getElementById('barrierModalBody');
        document.getElementById('barrierModalTitle').textContent = categoryName;
        document.getElementById('barrierModalSubtitle').textContent = 'Loading FGDs with this barrier…';
        body.innerHTML = '<div style="padding: 32px; text-align: center; color: var(--gray-500);">Loading…</div>';
        overlay.classList.add('active');

        const params = new URLSearchParams({ tab: currentTab, category: categoryId });
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        if (subsetUc && subsetUc !== 'all') params.append('subset_uc', subsetUc);

        try {
            const response = await fetch(`{{ route('admin.uc.barrier-records', $ucSlug) }}?${params.toString()}`);
            const result = await response.json();
            if (!result.success) throw new Error('Request failed');
            renderBarrierRecords(result);
        } catch (error) {
            console.error('Error loading barrier records:', error);
            document.getElementById('barrierModalSubtitle').textContent = '';
            body.innerHTML = '<div style="padding: 32px; text-align: center; color: #991b1b;">Error loading records.</div>';
        }
    }

    function renderBarrierRecords(result) {
        const body = document.getElementById('barrierModalBody');
        const records = result.records || [];
        const totalBarriers = records.reduce((sum, r) => sum + (r.barriers ? r.barriers.length : 0), 0);
        document.getElementById('barrierModalSubtitle').textContent =
            `${records.length} FGD session${records.length === 1 ? '' : 's'} · ${totalBarriers} barrier${totalBarriers === 1 ? '' : 's'} in this category`;

        if (!records.length) {
            body.innerHTML = '<div style="padding: 32px; text-align: center; color: var(--gray-500);">No FGDs raised a barrier in this category for the current filters.</div>';
            return;
        }

        let html = '';
        records.forEach(rec => {
            let barriersHtml = '';
            (rec.barriers || []).forEach(b => {
                const sr = (b.serial_number !== null && b.serial_number !== undefined) ? `${b.serial_number}. ` : '';
                barriersHtml += `<li>${sr}${escapeHtml(b.text)}</li>`;
            });
            html += `
                <div class="barrier-record-card">
                    <div class="barrier-record-head">
                        <code>${escapeHtml(rec.unique_id)}</code>
                        <span class="barrier-record-date">${escapeHtml(rec.date)}</span>
                    </div>
                    <div class="barrier-record-meta">
                        <span><strong>${escapeHtml(result.venue_label)}:</strong> ${escapeHtml(rec.venue || 'N/A')}</span>
                        <span><strong>UC:</strong> ${escapeHtml(rec.uc || 'N/A')}</span>
                        ${rec.district ? `<span><strong>District:</strong> ${escapeHtml(rec.district)}</span>` : ''}
                        ${rec.facilitator ? `<span><strong>Facilitator:</strong> ${escapeHtml(rec.facilitator)}</span>` : ''}
                    </div>
                    <ul class="barrier-record-list">${barriersHtml}</ul>
                </div>`;
        });
        body.innerHTML = html;
    }

    window.closeBarrierCategoryModal = function () {
        // The overlay's stopPropagation on the inner modal means this only fires
        // for the overlay backdrop or the close button.
        document.getElementById('barrierModalOverlay').classList.remove('active');
    };

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeBarrierCategoryModal();
    });

    // Render stats cards
    function renderStats(stats) {
        let html = '';
        const colors = ['primary', 'success', 'warning', 'info', 'purple', 'pink'];
        let i = 0;

        for (const [key, value] of Object.entries(stats)) {
            const label = formatLabel(key);
            const color = colors[i % colors.length];
            html += `
                <div class="tab-stat-card ${color}">
                    <span class="value">${formatNumber(value)}</span>
                    <span class="label">${label}</span>
                </div>
            `;
            i++;
        }

        tabStats.innerHTML = html;
    }

    // Render table
    function renderTable(records) {
        const columns = getColumnsForTab(currentTab);
        const titles = getTitleForTab(currentTab);

        tableTitle.textContent = titles.title;
        recordCount.textContent = `${records.length} records`;

        // Render header
        let headerHtml = '<tr>';
        columns.forEach(col => {
            headerHtml += `<th>${col.label}</th>`;
        });
        headerHtml += '<th>Actions</th></tr>';
        tableHead.innerHTML = headerHtml;

        // Render body
        if (records.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="${columns.length + 1}">
                        <div class="empty-state">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                            <p>No records found for the selected filters</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        let bodyHtml = '';
        records.forEach(record => {
            bodyHtml += '<tr>';
            columns.forEach(col => {
                let value = record[col.key] ?? 'N/A';
                if (col.key === 'unique_id') {
                    value = `<code>${value}</code>`;
                } else if (col.key === 'gender') {
                    const badgeClass = value === 'Male' ? 'badge-info' : 'badge-success';
                    value = `<span class="badge ${badgeClass}">${value}</span>`;
                } else if (col.breakdown) {
                    const males = record.participants_males ?? 0;
                    const females = record.participants_females ?? 0;
                    value = `<span class="badge badge-primary">${record.total_participants ?? 0}</span> <span class="text-muted">(M: ${males}, F: ${females})</span>`;
                } else if (col.key === 'total_participants' || col.key === 'barriers_count' || col.key === 'action_plans_count' || col.key === 'iit_members_count') {
                    value = `<span class="badge badge-primary">${value}</span>`;
                }
                bodyHtml += `<td>${value}</td>`;
            });
            bodyHtml += `<td class="action-links">
                <a href="${getViewUrl(record.id)}" class="action-link">View</a>
                <a href="${getEditUrl(record.id)}" class="action-link edit-link">Edit</a>
            </td>`;
            bodyHtml += '</tr>';
        });

        tableBody.innerHTML = bodyHtml;
    }

    // Get columns based on current tab
    function getColumnsForTab(tab) {
        switch (tab) {
            case 'fgds_community':
                return [
                    { key: 'unique_id', label: 'Form ID' },
                    { key: 'date', label: 'Date' },
                    { key: 'venue', label: 'Venue' },
                    { key: 'uc', label: 'UC' },
                    { key: 'total_participants', label: 'Participants', breakdown: true },
                    { key: 'barriers_count', label: 'Barriers' },
                    { key: 'submitted_by', label: 'Submitted By' },
                    { key: 'created_at', label: 'Created' }
                ];
            case 'fgds_health_workers':
                return [
                    { key: 'unique_id', label: 'Form ID' },
                    { key: 'date', label: 'Date' },
                    { key: 'hfs', label: 'Health Facility' },
                    { key: 'uc', label: 'UC' },
                    { key: 'total_participants', label: 'Participants', breakdown: true },
                    { key: 'barriers_count', label: 'Barriers' },
                    { key: 'submitted_by', label: 'Submitted By' },
                    { key: 'created_at', label: 'Created' }
                ];
            case 'bridging_the_gap':
                return [
                    { key: 'unique_id', label: 'Form ID' },
                    { key: 'date', label: 'Date' },
                    { key: 'venue', label: 'Venue' },
                    { key: 'uc', label: 'UC' },
                    { key: 'total_participants', label: 'Attendance' },
                    { key: 'iit_members_count', label: 'IIT Members' },
                    { key: 'action_plans_count', label: 'Action Plans' },
                    { key: 'created_at', label: 'Created' }
                ];
            case 'child_line_list':
                return [
                    { key: 'unique_id', label: 'Form ID' },
                    { key: 'child_name', label: 'Child Name' },
                    { key: 'father_name', label: 'Father Name' },
                    { key: 'gender', label: 'Gender' },
                    { key: 'age_in_months', label: 'Age (months)' },
                    { key: 'type', label: 'Type' },
                    { key: 'uc', label: 'UC' },
                    { key: 'created_at', label: 'Created' }
                ];
            default:
                return [];
        }
    }

    // Get title for tab
    function getTitleForTab(tab) {
        switch (tab) {
            case 'fgds_community':
                return { title: 'FGDs Community Sessions' };
            case 'fgds_health_workers':
                return { title: 'FGDs Health Workers Sessions' };
            case 'bridging_the_gap':
                return { title: 'Bridging The Gap Sessions' };
            case 'child_line_list':
                return { title: 'Child Line List Records' };
            default:
                return { title: 'Records' };
        }
    }

    // Get view URL based on tab
    function getViewUrl(id) {
        switch (currentTab) {
            case 'fgds_community':
                return `{{ url('admin/fgds-community') }}/${id}`;
            case 'fgds_health_workers':
                return `{{ url('admin/fgds-health-workers') }}/${id}`;
            case 'bridging_the_gap':
                return `{{ url('admin/bridging-the-gap') }}/${id}`;
            case 'child_line_list':
                return `{{ url('admin/child-line-list') }}/${id}`;
            default:
                return '#';
        }
    }

    // Get edit URL based on tab
    function getEditUrl(id) {
        switch (currentTab) {
            case 'fgds_community':
                return `{{ url('admin/fgds-community') }}/${id}/edit`;
            case 'fgds_health_workers':
                return `{{ url('admin/fgds-health-workers') }}/${id}/edit`;
            case 'bridging_the_gap':
                return `{{ url('admin/bridging-the-gap') }}/${id}/edit`;
            case 'child_line_list':
                return `{{ url('admin/child-line-list') }}/${id}/edit`;
            default:
                return '#';
        }
    }

    // Format label (convert snake_case to Title Case)
    function formatLabel(key) {
        return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    // Format number with commas
    function formatNumber(num) {
        return new Intl.NumberFormat().format(num);
    }

    // Load initial data
    loadData();
});
</script>
