{{--
    Shared action-plan manager for the Bridging The Gap index and detail pages.
    Open it with openActionPlanModal(recordId, uniqueId). Each plan item has an
    implementation status, follow-up remarks and supporting activity pictures.
    If anything changed, closing the dialog reloads the page so the server-
    rendered tables and counts are current.
--}}
<dialog id="actionPlanModal" class="modal modal-wide">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Manage Action Plans — <span id="apRecordId"></span></h3>
            <button type="button" class="modal-close" onclick="closeActionPlanModal()">&times;</button>
        </div>
        <div class="modal-body" style="max-height: 72vh; overflow-y: auto;">
            <div id="apSummary" class="ap-summary"></div>
            <div id="apList" class="ap-list"></div>

            <div class="ap-divider">
                <div class="ap-section-title">Add New Action Plan</div>
                <div id="apNewForm" class="ap-item-fields"></div>
                <button type="button" class="btn btn-sm btn-primary" onclick="addActionPlan()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Add Action Plan
                </button>
            </div>

            <div class="ap-divider">
                <div class="ap-section-title">Or Upload via Excel</div>
                <div style="background: #dcfce7; border: 1px solid #86efac; border-radius: 8px; padding: 10px; margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 13px; color: #166534;">Need the correct format?</span>
                    <a href="{{ route('admin.bridging-the-gap.action-plan-sample') }}" class="btn btn-sm btn-success" style="margin-left: auto;">Download Sample</a>
                </div>
                <div style="display: flex; gap: 10px; align-items: flex-end;">
                    <div style="flex: 1;">
                        <input type="file" id="apExcelFile" accept=".xlsx,.xls" class="form-input" style="width: 100%;">
                    </div>
                    <button type="button" class="btn btn-sm btn-success" onclick="uploadActionPlanExcel()">Upload Excel</button>
                </div>
                <p class="ap-hint">Uploading an Excel file <strong>replaces</strong> every action plan of this record — including their implementation status, remarks and activity pictures. To revise a plan after a follow-up meeting, use <em>Edit</em> on the item instead.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-danger" id="apDeleteAllBtn" onclick="deleteAllActionPlans()">Delete All</button>
            <span style="flex: 1;"></span>
            <button type="button" class="btn btn-outline" onclick="closeActionPlanModal()">Close</button>
        </div>
    </div>
</dialog>

<script>
(function () {
    const AP_URL = @json(url('admin/bridging-the-gap'));
    const AP_CSRF = @json(csrf_token());
    const AP_STATUSES = @json(\App\Models\BridgingTheGapActionPlan::STATUSES);
    const AP_DEFAULT_STATUS = @json(\App\Models\BridgingTheGapActionPlan::DEFAULT_STATUS);

    // Canonical action-plan columns, in display order. Cards, the inline editor,
    // the add form and the save payload are all driven off this list. Keep in
    // sync with BridgingTheGapController::resolveActionPlanColumns() and the
    // sample template it generates.
    const AP_FIELDS = [
        { key: 'problem',            label: 'Problem',       type: 'textarea' },
        { key: 'sub_cause',          label: 'Sub Cause',     type: 'textarea' },
        { key: 'root_cause',         label: 'Root Cause',    type: 'textarea' },
        { key: 'solution',           label: 'Solution',      type: 'textarea' },
        { key: 'action_needed',      label: 'Action Needed', type: 'input' },
        { key: 'who_is_responsible', label: 'Responsible',   type: 'input' },
        { key: 'timeline',           label: 'Timeline',      type: 'input' },
    ];

    const dialog = document.getElementById('actionPlanModal');
    const listEl = document.getElementById('apList');
    const summaryEl = document.getElementById('apSummary');

    let recordId = null;
    let plans = [];
    let editingId = null;
    let dirty = false;

    /* ---------------- helpers ---------------- */

    function esc(str) {
        const div = document.createElement('div');
        div.textContent = str == null ? '' : String(str);
        return div.innerHTML;
    }

    function escAttr(str) {
        return esc(str).replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function fmtDate(iso) {
        if (!iso) return '';
        const d = new Date(iso);
        return isNaN(d) ? '' : d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
    }

    function statusOptions(selected) {
        return Object.entries(AP_STATUSES)
            .map(([value, label]) => '<option value="' + value + '"' + (value === selected ? ' selected' : '') + '>' + esc(label) + '</option>')
            .join('');
    }

    function statusBadge(status) {
        const key = AP_STATUSES[status] ? status : AP_DEFAULT_STATUS;
        return '<span class="ap-status ap-status-' + key + '">' + esc(AP_STATUSES[key]) + '</span>';
    }

    async function apFetch(url, options) {
        options = options || {};
        const headers = Object.assign({
            'Accept': 'application/json',
            'X-CSRF-TOKEN': AP_CSRF,
            'X-Requested-With': 'XMLHttpRequest',
        }, options.headers || {});

        const res = await fetch(url, Object.assign({}, options, { headers }));
        let data = null;
        try { data = await res.json(); } catch (e) { data = null; }

        if (!res.ok || (data && data.success === false)) {
            let message = (data && data.message) || ('Request failed (' + res.status + ')');
            if (data && data.errors) message = Object.values(data.errors).flat().join('\n');
            if (res.status === 413) message = 'That file is too large for the server.';
            throw new Error(message);
        }
        return data || {};
    }

    function jsonOptions(method, body) {
        return { method, headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body) };
    }

    function fieldInput(field, value, idPrefix) {
        const id = idPrefix + field.key;
        return field.type === 'textarea'
            ? '<textarea id="' + id + '" class="form-input" rows="2">' + esc(value || '') + '</textarea>'
            : '<input type="text" id="' + id + '" class="form-input" value="' + escAttr(value || '') + '">';
    }

    /* ---------------- open / close ---------------- */

    window.openActionPlanModal = function (id, uniqueId) {
        recordId = id;
        editingId = null;
        document.getElementById('apRecordId').textContent = uniqueId;
        renderNewForm();
        dialog.showModal();
        loadActionPlans();
    };

    window.closeActionPlanModal = function () {
        dialog.close();
    };

    dialog.addEventListener('close', function () {
        if (dirty) window.location.reload();
    });

    /* ---------------- load & render ---------------- */

    async function loadActionPlans() {
        listEl.innerHTML = '<div style="text-align:center; padding:20px; color:#9ca3af;">Loading action plans…</div>';
        summaryEl.innerHTML = '';
        try {
            const data = await apFetch(AP_URL + '/' + recordId + '/action-plans');
            plans = data.action_plans || [];
            render();
        } catch (e) {
            listEl.innerHTML = '<div style="text-align:center; padding:16px; color:#ef4444;">Failed to load action plans.</div>';
        }
    }

    function render() {
        document.getElementById('apDeleteAllBtn').style.display = plans.length ? '' : 'none';

        if (!plans.length) {
            summaryEl.innerHTML = '';
            listEl.innerHTML = '<div style="text-align:center; padding:16px; color:#9ca3af; font-style:italic;">No action plans yet. Add one below or upload an Excel file.</div>';
            return;
        }

        const counts = {};
        plans.forEach(p => { counts[p.implementation_status] = (counts[p.implementation_status] || 0) + 1; });
        summaryEl.innerHTML = Object.keys(AP_STATUSES)
            .map(key => '<span class="ap-status ap-status-' + key + '">' + esc(AP_STATUSES[key]) + ': ' + (counts[key] || 0) + '</span>')
            .join('');

        listEl.innerHTML = plans.map(p => p.id === editingId ? editCard(p) : viewCard(p)).join('');
    }

    function photosHtml(plan) {
        const thumbs = (plan.photos || []).map(photo =>
            '<div class="ap-thumb">' +
                '<a href="' + escAttr(photo.url) + '" target="_blank" rel="noopener" title="' + escAttr(photo.original_name || 'Activity picture') + '">' +
                    '<img src="' + escAttr(photo.url) + '" alt="" loading="lazy">' +
                '</a>' +
                '<button type="button" class="ap-thumb-remove" title="Delete picture" onclick="apRemovePhoto(' + plan.id + ', ' + photo.id + ')">&times;</button>' +
            '</div>'
        ).join('');

        return '<div class="ap-photos-row">' +
            '<label>Supporting activity pictures (' + (plan.photos || []).length + ')</label>' +
            '<div class="ap-photos">' + thumbs +
                '<label class="ap-photo-add" id="ap-add-' + plan.id + '">+ Add pictures' +
                    '<input type="file" accept="image/jpeg,image/png,image/webp" multiple onchange="apUpload(' + plan.id + ', this)">' +
                '</label>' +
            '</div>' +
        '</div>';
    }

    function viewCard(plan) {
        const meta = plan.status_updated_at ? '<span class="ap-status-meta">Status updated ' + esc(fmtDate(plan.status_updated_at)) + '</span>' : '';
        const fields = AP_FIELDS.filter(f => f.key !== 'problem').map(f =>
            '<div class="ap-field"><label>' + esc(f.label) + '</label><div>' + (plan[f.key] ? esc(plan[f.key]) : '—') + '</div></div>'
        ).join('');
        const remarks = plan.implementation_remarks
            ? '<div class="ap-field ap-field-wide"><label>Follow-up remarks</label><div>' + esc(plan.implementation_remarks) + '</div></div>'
            : '';

        return '<div class="ap-item" id="ap-item-' + plan.id + '">' +
            '<div class="ap-item-head">' +
                '<span class="ap-item-sn">#' + esc(plan.serial_number || '-') + '</span>' +
                '<div class="ap-item-title">' + esc(plan.problem) + meta + '</div>' +
                '<div class="ap-item-tools">' +
                    '<select class="ap-status-select ap-status-' + esc(plan.implementation_status) + '" title="Implementation status" onchange="apStatus(' + plan.id + ', this)">' + statusOptions(plan.implementation_status) + '</select>' +
                    '<button type="button" class="btn btn-sm btn-outline" onclick="apEdit(' + plan.id + ')">Edit</button>' +
                    '<button type="button" class="btn btn-sm btn-danger" onclick="apDelete(' + plan.id + ')">Delete</button>' +
                '</div>' +
            '</div>' +
            '<div class="ap-item-fields">' + fields + remarks + '</div>' +
            photosHtml(plan) +
        '</div>';
    }

    function editCard(plan) {
        const prefix = 'ap-edit-' + plan.id + '-';
        const fields = AP_FIELDS.map(f =>
            '<div class="ap-field' + (f.key === 'problem' ? ' ap-field-wide' : '') + '"><label>' + esc(f.label) + (f.key === 'problem' ? ' *' : '') + '</label>' + fieldInput(f, plan[f.key], prefix) + '</div>'
        ).join('');

        return '<div class="ap-item" id="ap-item-' + plan.id + '">' +
            '<div class="ap-item-head">' +
                '<span class="ap-item-sn">#' + esc(plan.serial_number || '-') + '</span>' +
                '<div class="ap-item-title">Editing action plan</div>' +
                '<div class="ap-item-tools">' +
                    '<button type="button" class="btn btn-sm btn-success" onclick="apSave(' + plan.id + ')">Save</button>' +
                    '<button type="button" class="btn btn-sm btn-outline" onclick="apCancel()">Cancel</button>' +
                '</div>' +
            '</div>' +
            '<div class="ap-item-fields">' + fields +
                '<div class="ap-field"><label>Implementation Status</label><select id="' + prefix + 'implementation_status" class="form-input">' + statusOptions(plan.implementation_status) + '</select></div>' +
                '<div class="ap-field ap-field-wide"><label>Follow-up remarks</label><textarea id="' + prefix + 'implementation_remarks" class="form-input" rows="2" placeholder="Outcome of the community follow-up meeting, progress notes…">' + esc(plan.implementation_remarks || '') + '</textarea></div>' +
            '</div>' +
            photosHtml(plan) +
        '</div>';
    }

    function renderNewForm() {
        const prefix = 'ap-new-';
        document.getElementById('apNewForm').innerHTML =
            AP_FIELDS.map(f =>
                '<div class="ap-field' + (f.key === 'problem' ? ' ap-field-wide' : '') + '"><label>' + esc(f.label) + (f.key === 'problem' ? ' *' : '') + '</label>' + fieldInput(f, '', prefix) + '</div>'
            ).join('') +
            '<div class="ap-field"><label>Implementation Status</label><select id="' + prefix + 'implementation_status" class="form-input">' + statusOptions(AP_DEFAULT_STATUS) + '</select></div>' +
            '<div class="ap-field ap-field-wide"><label>Follow-up remarks</label><textarea id="' + prefix + 'implementation_remarks" class="form-input" rows="2"></textarea></div>';
    }

    function collect(prefix) {
        const body = {};
        AP_FIELDS.forEach(f => {
            const el = document.getElementById(prefix + f.key);
            body[f.key] = el ? (el.value.trim() || null) : null;
        });
        body.implementation_status = document.getElementById(prefix + 'implementation_status').value;
        body.implementation_remarks = document.getElementById(prefix + 'implementation_remarks').value.trim() || null;
        return body;
    }

    /* ---------------- actions ---------------- */

    window.addActionPlan = async function () {
        const body = collect('ap-new-');
        if (!body.problem) { alert('Problem field is required.'); return; }
        try {
            await apFetch(AP_URL + '/' + recordId + '/action-plans', jsonOptions('POST', body));
            dirty = true;
            renderNewForm();
            loadActionPlans();
        } catch (e) {
            alert(e.message);
        }
    };

    window.apEdit = function (id) {
        editingId = id;
        render();
    };

    window.apCancel = function () {
        editingId = null;
        render();
    };

    window.apSave = async function (id) {
        const body = collect('ap-edit-' + id + '-');
        if (!body.problem) { alert('Problem field is required.'); return; }
        try {
            await apFetch(AP_URL + '/action-plans/' + id, jsonOptions('PUT', body));
            dirty = true;
            editingId = null;
            loadActionPlans();
        } catch (e) {
            alert(e.message);
        }
    };

    window.apStatus = async function (id, select) {
        const previous = (plans.find(p => p.id === id) || {}).implementation_status;
        select.disabled = true;
        try {
            const data = await apFetch(AP_URL + '/action-plans/' + id, jsonOptions('PUT', { implementation_status: select.value }));
            dirty = true;
            plans = plans.map(p => p.id === id ? data.action_plan : p);
            render();
        } catch (e) {
            alert(e.message);
            select.value = previous;
            select.disabled = false;
        }
    };

    window.apDelete = async function (id) {
        if (!confirm('Delete this action plan, including its activity pictures?')) return;
        try {
            await apFetch(AP_URL + '/action-plans/' + id, { method: 'DELETE' });
            dirty = true;
            loadActionPlans();
        } catch (e) {
            alert(e.message);
        }
    };

    window.deleteAllActionPlans = async function () {
        if (!confirm('Delete ALL action plans for this record, including their activity pictures? This cannot be undone.')) return;
        try {
            await apFetch(AP_URL + '/' + recordId + '/action-plans', { method: 'DELETE' });
            dirty = true;
            loadActionPlans();
        } catch (e) {
            alert(e.message);
        }
    };

    /* ---------------- activity pictures ---------------- */

    // Phone photos are often several MB but the server accepts 2 MB per file,
    // so images are downscaled in the browser (longest side 1600px, JPEG).
    function downscaleImage(file, maxDim, quality) {
        maxDim = maxDim || 1600;
        quality = quality || 0.82;
        return new Promise(resolve => {
            if (!file.type || !file.type.startsWith('image/')) return resolve(file);
            const url = URL.createObjectURL(file);
            const img = new Image();
            img.onload = function () {
                const scale = Math.min(1, maxDim / Math.max(img.width, img.height));
                if (scale === 1 && file.size <= 1.5 * 1024 * 1024) {
                    URL.revokeObjectURL(url);
                    return resolve(file);
                }
                const canvas = document.createElement('canvas');
                canvas.width = Math.round(img.width * scale);
                canvas.height = Math.round(img.height * scale);
                const ctx = canvas.getContext('2d');
                ctx.fillStyle = '#fff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                canvas.toBlob(blob => {
                    URL.revokeObjectURL(url);
                    if (!blob || (blob.size >= file.size && file.size <= 2 * 1024 * 1024)) return resolve(file);
                    resolve(new File([blob], file.name.replace(/\.[^.]+$/, '') + '.jpg', { type: 'image/jpeg' }));
                }, 'image/jpeg', quality);
            };
            img.onerror = function () { URL.revokeObjectURL(url); resolve(file); };
            img.src = url;
        });
    }

    window.apUpload = async function (planId, input) {
        const files = Array.from(input.files || []);
        if (!files.length) return;

        const button = document.getElementById('ap-add-' + planId);
        const errors = [];
        button.classList.add('is-busy');

        for (let i = 0; i < files.length; i++) {
            button.firstChild.textContent = 'Uploading ' + (i + 1) + '/' + files.length + '…';
            try {
                const form = new FormData();
                form.append('photo', await downscaleImage(files[i]));
                await apFetch(AP_URL + '/action-plans/' + planId + '/photos', { method: 'POST', body: form });
                dirty = true;
            } catch (e) {
                errors.push(files[i].name + ': ' + e.message);
            }
        }

        input.value = '';
        if (errors.length) alert('Some pictures were not uploaded:\n\n' + errors.join('\n'));
        await loadActionPlans();
    };

    window.apRemovePhoto = async function (planId, photoId) {
        if (!confirm('Delete this activity picture?')) return;
        try {
            await apFetch(AP_URL + '/action-plan-photos/' + photoId, { method: 'DELETE' });
            dirty = true;
            plans = plans.map(p => p.id === planId ? Object.assign({}, p, { photos: (p.photos || []).filter(ph => ph.id !== photoId) }) : p);
            render();
        } catch (e) {
            alert(e.message);
        }
    };

    /* ---------------- Excel upload ---------------- */

    window.uploadActionPlanExcel = async function () {
        const fileInput = document.getElementById('apExcelFile');
        if (!fileInput.files.length) { alert('Please select an Excel file.'); return; }
        if (!confirm('This will REPLACE all existing action plans for this record, including their status, remarks and activity pictures. Continue?')) return;

        const formData = new FormData();
        formData.append('action_plan_file', fileInput.files[0]);

        try {
            const data = await apFetch(AP_URL + '/' + recordId + '/action-plan', { method: 'POST', body: formData });
            dirty = true;
            fileInput.value = '';
            alert(data.message || 'Action plans imported.');
            loadActionPlans();
        } catch (e) {
            alert(e.message);
        }
    };
})();
</script>
