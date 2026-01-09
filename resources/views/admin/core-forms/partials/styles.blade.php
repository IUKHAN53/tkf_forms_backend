@push('styles')
<style>
/* ===== Modern Statistics Cards Grid ===== */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 20px;
}

.stat-card {
    background: white;
    border-radius: 14px;
    padding: 18px 20px;
    display: flex;
    align-items: flex-start;
    gap: 14px;
    border: 1px solid var(--gray-100, #f3f4f6);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 1px 2px rgba(0, 0, 0, 0.02);
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
    border-radius: 14px 14px 0 0;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.stat-card-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.stat-card-icon svg {
    width: 22px;
    height: 22px;
}

.stat-card-content {
    flex: 1;
    min-width: 0;
}

.stat-card-value {
    display: block;
    font-size: 26px;
    font-weight: 700;
    line-height: 1.2;
    letter-spacing: -0.5px;
}

.stat-card-label {
    display: block;
    font-size: 13px;
    font-weight: 500;
    margin-top: 2px;
    opacity: 0.85;
}

.stat-card-trend {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 6px;
    white-space: nowrap;
}

.stat-card-trend svg {
    width: 12px;
    height: 12px;
}

/* Primary Card */
.stat-card-primary::before {
    background: linear-gradient(90deg, #10b981, #34d399);
}

.stat-card-primary .stat-card-icon {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.08));
    color: #059669;
}

.stat-card-primary .stat-card-value {
    color: #047857;
}

.stat-card-primary .stat-card-label {
    color: #059669;
}

/* Success Card */
.stat-card-success::before {
    background: linear-gradient(90deg, #22c55e, #4ade80);
}

.stat-card-success .stat-card-icon {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.15), rgba(34, 197, 94, 0.08));
    color: #16a34a;
}

.stat-card-success .stat-card-value {
    color: #15803d;
}

.stat-card-success .stat-card-label {
    color: #16a34a;
}

/* Info Card */
.stat-card-info::before {
    background: linear-gradient(90deg, #3b82f6, #60a5fa);
}

.stat-card-info .stat-card-icon {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(59, 130, 246, 0.08));
    color: #2563eb;
}

.stat-card-info .stat-card-value {
    color: #1d4ed8;
}

.stat-card-info .stat-card-label {
    color: #2563eb;
}

/* Warning Card */
.stat-card-warning::before {
    background: linear-gradient(90deg, #f59e0b, #fbbf24);
}

.stat-card-warning .stat-card-icon {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(245, 158, 11, 0.08));
    color: #d97706;
}

.stat-card-warning .stat-card-value {
    color: #b45309;
}

.stat-card-warning .stat-card-label {
    color: #d97706;
}

/* Purple Card */
.stat-card-purple::before {
    background: linear-gradient(90deg, #8b5cf6, #a78bfa);
}

.stat-card-purple .stat-card-icon {
    background: linear-gradient(135deg, rgba(139, 92, 246, 0.15), rgba(139, 92, 246, 0.08));
    color: #7c3aed;
}

.stat-card-purple .stat-card-value {
    color: #6d28d9;
}

.stat-card-purple .stat-card-label {
    color: #7c3aed;
}

/* Trend indicators */
.stat-card-trend-up {
    background: rgba(34, 197, 94, 0.12);
    color: #16a34a;
}

.stat-card-trend-down {
    background: rgba(239, 68, 68, 0.12);
    color: #dc2626;
}

.stat-card-trend-neutral {
    background: rgba(107, 114, 128, 0.1);
    color: #6b7280;
}

/* Responsive adjustments */
@media (max-width: 1200px) {
    .stats-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 900px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 600px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }

    .stat-card {
        padding: 14px 16px;
    }

    .stat-card-icon {
        width: 40px;
        height: 40px;
    }

    .stat-card-icon svg {
        width: 20px;
        height: 20px;
    }

    .stat-card-value {
        font-size: 22px;
    }
}

/* ===== Modern Content Card ===== */
.content-card {
    background: white;
    border-radius: 16px;
    border: 1px solid var(--gray-200, #e5e7eb);
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
}

/* ===== Compact Card Header ===== */
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    background: white;
    border-bottom: 1px solid var(--gray-100, #f3f4f6);
    gap: 12px;
    flex-wrap: wrap;
}

.header-left h2 {
    font-size: 18px;
    font-weight: 700;
    color: var(--gray-900, #111827);
    margin: 0;
    letter-spacing: -0.25px;
    display: inline;
}

.header-left p {
    margin: 0;
    display: inline;
    margin-left: 12px;
    font-size: 13px;
    color: var(--gray-500, #6b7280);
}

.header-left {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px;
}

/* ===== Compact Header Actions (Icon Buttons) ===== */
.header-actions {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.header-actions .btn {
    padding: 8px 12px;
    font-size: 12px;
    border-radius: 8px;
    gap: 5px;
}

.header-actions .btn svg {
    width: 14px;
    height: 14px;
}

/* ===== Modern Buttons ===== */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 10px 16px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.15s ease;
    border: none;
    text-decoration: none;
    white-space: nowrap;
    font-family: inherit;
}

.btn:hover {
    transform: translateY(-1px);
}

.btn:active {
    transform: translateY(0);
}

.btn svg {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
}

.btn-sm {
    padding: 6px 10px;
    font-size: 12px;
    border-radius: 6px;
}

.btn-sm svg {
    width: 14px;
    height: 14px;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-500, #10b981), var(--primary-600, #059669));
    color: white;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.btn-primary:hover {
    background: linear-gradient(135deg, var(--primary-600, #059669), var(--primary-700, #047857));
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
}

.btn-outline {
    background: white;
    border: 1px solid var(--gray-200, #e5e7eb);
    color: var(--gray-600, #4b5563);
}

.btn-outline:hover {
    background: var(--gray-50, #f9fafb);
    border-color: var(--gray-300, #d1d5db);
    color: var(--gray-700, #374151);
}

.btn-danger {
    background: linear-gradient(135deg, var(--error, #ef4444), #dc2626);
    color: white;
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
}

.btn-danger:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
}

.btn-success {
    background: linear-gradient(135deg, var(--success, #10b981), #059669);
    color: white;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.btn-success:hover {
    background: linear-gradient(135deg, #059669, #047857);
}

.btn-warning {
    background: linear-gradient(135deg, var(--warning, #f59e0b), #d97706);
    color: white;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
}

.btn-warning:hover {
    background: linear-gradient(135deg, #d97706, #b45309);
}

.btn-info {
    background: linear-gradient(135deg, var(--info, #3b82f6), #2563eb);
    color: white;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

.btn-info:hover {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
}

/* ===== Compact Card Filters ===== */
.card-filters {
    padding: 14px 20px;
    background: var(--gray-50, #f9fafb);
    border-bottom: 1px solid var(--gray-100, #f3f4f6);
}

.filter-form {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
}

.filter-row {
    display: contents;
}

/* ===== Compact Form Inputs ===== */
.form-input,
select.form-input,
textarea.form-input {
    padding: 8px 12px;
    border: 1px solid var(--gray-200, #e5e7eb);
    border-radius: 8px;
    font-size: 13px;
    background: white;
    color: var(--gray-700, #374151);
    transition: all 0.15s ease;
    font-family: inherit;
    min-width: 0;
    height: 36px;
}

.form-input:hover {
    border-color: var(--gray-300, #d1d5db);
}

.form-input:focus {
    outline: none;
    border-color: var(--primary-500, #10b981);
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.form-input::placeholder {
    color: var(--gray-400, #9ca3af);
}

/* Search input - wider */
.filter-form input[name="search"] {
    min-width: 260px;
    flex: 1;
    max-width: 320px;
}

/* Select dropdowns */
select.form-input {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    padding-right: 32px;
    cursor: pointer;
    min-width: 130px;
}

.filter-select {
    min-width: 130px;
    max-width: 160px;
}

/* Date filters - compact inline */
.date-filter {
    display: flex;
    align-items: center;
    gap: 6px;
}

.date-filter label {
    font-size: 12px;
    font-weight: 500;
    color: var(--gray-500, #6b7280);
    white-space: nowrap;
}

.date-filter .form-input {
    min-width: 130px;
    width: 130px;
}

/* Facilitator/other text inputs in filter */
.filter-form input[name="facilitator"],
.filter-form input[name="venue"] {
    min-width: 140px;
    max-width: 180px;
}

/* Filter buttons */
.filter-form .btn {
    height: 36px;
    padding: 0 14px;
}

textarea.form-input {
    resize: vertical;
    min-height: 80px;
    height: auto;
}

.form-group {
    margin-bottom: 16px;
}

.form-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-700, #374151);
    margin-bottom: 6px;
}

.form-helper {
    font-size: 12px;
    color: var(--gray-500, #6b7280);
    margin-top: 4px;
}

.form-error {
    font-size: 12px;
    color: var(--error, #ef4444);
    margin-top: 4px;
}

/* ===== Data Table ===== */
.table-container {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th,
.data-table td {
    padding: 12px 14px;
    text-align: left;
    border-bottom: 1px solid var(--gray-100, #f3f4f6);
}

.data-table th {
    background: var(--gray-50, #f9fafb);
    font-weight: 600;
    color: var(--gray-500, #6b7280);
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
}

.data-table td {
    color: var(--gray-700, #374151);
    font-size: 13px;
    vertical-align: middle;
}

.data-table td code {
    background: var(--primary-50, #ecfdf5);
    padding: 3px 6px;
    border-radius: 4px;
    font-size: 11px;
    font-family: 'SF Mono', Monaco, 'Courier New', monospace;
    color: var(--primary-700, #047857);
}

.data-table tbody tr {
    transition: background 0.1s ease;
}

.data-table tbody tr:hover {
    background: var(--gray-50, #f9fafb);
}

.data-table tbody tr:last-child td {
    border-bottom: none;
}

/* ===== Action Buttons in Table ===== */
.action-buttons {
    display: flex;
    gap: 6px;
    align-items: center;
}

.action-buttons form {
    margin: 0;
}

/* ===== Compact Badges ===== */
.badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    line-height: 1;
}

.badge-primary {
    background: var(--primary-100, #d1fae5);
    color: var(--primary-800, #065f46);
}

.badge-success {
    background: var(--success-light, #d1fae5);
    color: #065f46;
}

.badge-danger {
    background: var(--error-light, #fee2e2);
    color: #991b1b;
}

.badge-warning {
    background: var(--warning-light, #fef3c7);
    color: #92400e;
}

.badge-info {
    background: var(--info-light, #dbeafe);
    color: #1e40af;
}

/* ===== Card Footer ===== */
.card-footer {
    padding: 12px 20px;
    border-top: 1px solid var(--gray-100, #f3f4f6);
    background: var(--gray-50, #f9fafb);
}

/* ===== Text Utilities ===== */
.text-center {
    text-align: center;
}

.text-muted {
    color: var(--gray-500, #6b7280);
}

.mb-md {
    margin-bottom: 16px;
}

/* ===== Compact Modal ===== */
.modal {
    border: none;
    border-radius: 12px;
    padding: 0;
    max-width: 480px;
    width: 90%;
    box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.25);
    overflow: hidden;
}

.modal::backdrop {
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
}

.modal-content {
    background: white;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid var(--gray-100, #f3f4f6);
}

.modal-header h3 {
    font-size: 16px;
    font-weight: 700;
    color: var(--gray-900, #111827);
    margin: 0;
}

.modal-close {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: var(--gray-400, #9ca3af);
    border-radius: 6px;
    transition: all 0.15s ease;
}

.modal-close:hover {
    background: var(--gray-100, #f3f4f6);
    color: var(--gray-600, #4b5563);
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 16px 20px;
    border-top: 1px solid var(--gray-100, #f3f4f6);
    background: var(--gray-50, #f9fafb);
}

/* ===== Detail View ===== */
.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    padding: 20px;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.detail-item label {
    font-size: 11px;
    font-weight: 600;
    color: var(--gray-500, #6b7280);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-item span {
    font-size: 14px;
    color: var(--gray-800, #1f2937);
    line-height: 1.4;
}

/* ===== Participants Section ===== */
.participants-section {
    padding: 20px;
    border-top: 1px solid var(--gray-100, #f3f4f6);
}

.participants-section h3 {
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 14px;
    color: var(--gray-900, #111827);
    display: flex;
    align-items: center;
    gap: 8px;
}

/* ===== Form Grid Layouts ===== */
.form-container {
    padding: 20px;
}

.form-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.form-grid-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

.form-actions {
    display: flex;
    gap: 10px;
    padding-top: 14px;
    margin-top: 20px;
    border-top: 1px solid var(--gray-200, #e5e7eb);
}

.form-section-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--gray-900, #111827);
    margin-bottom: 16px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--primary-500, #10b981);
    display: inline-block;
}

/* ===== Checkbox and Radio ===== */
input[type="checkbox"],
input[type="radio"] {
    width: 16px;
    height: 16px;
    cursor: pointer;
    accent-color: var(--primary-500, #10b981);
}

/* ===== Compact Pagination ===== */
.pagination-wrapper {
    padding: 14px 20px;
    border-top: 1px solid var(--gray-100, #f3f4f6);
    background: var(--gray-50, #f9fafb);
}

.pagination-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}

.pagination-info {
    font-size: 13px;
    color: var(--gray-500, #6b7280);
}

.pagination-info .font-medium {
    font-weight: 600;
    color: var(--gray-700, #374151);
}

.pagination-numbers {
    display: flex;
    gap: 4px;
    align-items: center;
}

.pagination-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 34px;
    height: 34px;
    padding: 0 10px;
    border: 1px solid var(--gray-200, #e5e7eb);
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    color: var(--gray-600, #4b5563);
    background: white;
    text-decoration: none;
    transition: all 0.15s ease;
    cursor: pointer;
}

.pagination-btn:hover:not(.pagination-btn-disabled):not(.pagination-btn-active) {
    background: var(--primary-500, #10b981);
    border-color: var(--primary-500, #10b981);
    color: white;
}

.pagination-btn-active {
    background: linear-gradient(135deg, var(--primary-500, #10b981), var(--primary-600, #059669));
    color: white;
    border-color: var(--primary-500, #10b981);
    font-weight: 600;
}

.pagination-btn-disabled {
    opacity: 0.4;
    cursor: not-allowed;
    pointer-events: none;
}

.pagination-btn svg {
    width: 14px;
    height: 14px;
}

.pagination-dots {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 34px;
    height: 34px;
    color: var(--gray-400, #9ca3af);
    font-weight: bold;
    letter-spacing: 2px;
}

/* ===== File Upload Styling ===== */
.upload-info {
    background: var(--gray-50, #f9fafb);
    border-radius: 8px;
    padding: 12px;
    font-size: 12px;
    color: var(--gray-500, #6b7280);
    border: 1px dashed var(--gray-200, #e5e7eb);
}

.upload-info p {
    margin: 0 0 6px 0;
}

.upload-info p:last-child {
    margin-bottom: 0;
}

input[type="file"].form-input {
    padding: 8px 10px;
    cursor: pointer;
    height: auto;
}

input[type="file"].form-input::file-selector-button {
    background: linear-gradient(135deg, var(--primary-500, #10b981), var(--primary-600, #059669));
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 5px;
    font-weight: 600;
    font-size: 12px;
    cursor: pointer;
    margin-right: 10px;
    transition: all 0.15s ease;
}

input[type="file"].form-input::file-selector-button:hover {
    background: linear-gradient(135deg, var(--primary-600, #059669), var(--primary-700, #047857));
}

/* ===== Responsive ===== */
@media (max-width: 1024px) {
    .filter-form {
        gap: 8px;
    }

    .filter-form input[name="search"] {
        max-width: 100%;
        min-width: 200px;
    }
}

@media (max-width: 768px) {
    .card-header {
        padding: 14px 16px;
        flex-direction: column;
        align-items: flex-start;
    }

    .header-left {
        flex-direction: column;
        align-items: flex-start;
        gap: 2px;
    }

    .header-left p {
        margin-left: 0;
    }

    .header-actions {
        width: 100%;
    }

    .header-actions .btn {
        flex: 1;
        justify-content: center;
    }

    .card-filters {
        padding: 12px 16px;
    }

    .filter-form {
        flex-direction: column;
        align-items: stretch;
    }

    .filter-form input[name="search"],
    .filter-select,
    .form-input {
        min-width: 100%;
        max-width: 100%;
        width: 100%;
    }

    .date-filter {
        width: 100%;
    }

    .date-filter .form-input {
        flex: 1;
        min-width: 0;
        width: auto;
    }

    .filter-form .btn {
        width: 100%;
    }

    .form-grid-2,
    .form-grid-3 {
        grid-template-columns: 1fr;
    }

    .modal {
        width: 95%;
        max-width: none;
        margin: 12px;
    }

    .detail-grid {
        grid-template-columns: 1fr;
    }

    .data-table th,
    .data-table td {
        padding: 10px 12px;
    }
}
</style>
@endpush
