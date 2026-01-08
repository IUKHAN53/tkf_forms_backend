@push('styles')
<style>
/* ===== Modern Content Card ===== */
.content-card {
    background: white;
    border-radius: 16px;
    border: 1px solid var(--gray-200, #e5e7eb);
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
}

/* ===== Card Header ===== */
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 24px;
    background: linear-gradient(to right, var(--gray-50, #f9fafb), white);
    border-bottom: 1px solid var(--gray-200, #e5e7eb);
    gap: 16px;
    flex-wrap: wrap;
}

.header-left h2 {
    font-size: 20px;
    font-weight: 700;
    color: var(--gray-900, #111827);
    margin: 0 0 4px 0;
    letter-spacing: -0.25px;
}

.header-left p {
    margin: 0;
}

.header-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

/* ===== Modern Buttons ===== */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
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
    width: 18px;
    height: 18px;
    flex-shrink: 0;
}

.btn-sm {
    padding: 8px 14px;
    font-size: 13px;
    border-radius: 8px;
}

.btn-sm svg {
    width: 16px;
    height: 16px;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-500, #10b981), var(--primary-600, #059669));
    color: white;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
}

.btn-primary:hover {
    background: linear-gradient(135deg, var(--primary-600, #059669), var(--primary-700, #047857));
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
}

.btn-outline {
    background: white;
    border: 1px solid var(--gray-300, #d1d5db);
    color: var(--gray-700, #374151);
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.btn-outline:hover {
    background: var(--gray-50, #f9fafb);
    border-color: var(--gray-400, #9ca3af);
}

.btn-danger {
    background: linear-gradient(135deg, var(--error, #ef4444), #dc2626);
    color: white;
    box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35);
}

.btn-danger:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
}

.btn-success {
    background: linear-gradient(135deg, var(--success, #10b981), #059669);
    color: white;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
}

.btn-success:hover {
    background: linear-gradient(135deg, #059669, #047857);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
}

.btn-warning {
    background: linear-gradient(135deg, var(--warning, #f59e0b), #d97706);
    color: white;
    box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35);
}

.btn-warning:hover {
    background: linear-gradient(135deg, #d97706, #b45309);
}

.btn-info {
    background: linear-gradient(135deg, var(--info, #3b82f6), #2563eb);
    color: white;
    box-shadow: 0 4px 14px rgba(59, 130, 246, 0.35);
}

.btn-info:hover {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
}

/* ===== Card Filters ===== */
.card-filters {
    padding: 20px 24px;
    background: var(--gray-50, #f9fafb);
    border-bottom: 1px solid var(--gray-200, #e5e7eb);
}

.filter-form {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.filter-row {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: center;
}

.filter-select {
    min-width: 160px;
    max-width: 220px;
}

.date-filter {
    display: flex;
    align-items: center;
    gap: 10px;
}

.date-filter label {
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-600, #4b5563);
    white-space: nowrap;
}

/* ===== Modern Form Inputs ===== */
.form-input,
select.form-input,
textarea.form-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--gray-200, #e5e7eb);
    border-radius: 10px;
    font-size: 14px;
    background: white;
    color: var(--gray-800, #1f2937);
    transition: all 0.2s ease;
    font-family: inherit;
    min-width: 200px;
}

.form-input:hover {
    border-color: var(--gray-300, #d1d5db);
}

.form-input:focus {
    outline: none;
    border-color: var(--primary-500, #10b981);
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
}

.form-input::placeholder {
    color: var(--gray-400, #9ca3af);
}

select.form-input {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    padding-right: 44px;
    cursor: pointer;
}

textarea.form-input {
    resize: vertical;
    min-height: 100px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-700, #374151);
    margin-bottom: 8px;
}

.form-helper {
    font-size: 13px;
    color: var(--gray-500, #6b7280);
    margin-top: 6px;
}

.form-error {
    font-size: 13px;
    color: var(--error, #ef4444);
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
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
    padding: 14px 18px;
    text-align: left;
    border-bottom: 1px solid var(--gray-100, #f3f4f6);
}

.data-table th {
    background: var(--gray-50, #f9fafb);
    font-weight: 600;
    color: var(--gray-600, #4b5563);
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
}

.data-table td {
    color: var(--gray-700, #374151);
    font-size: 14px;
    vertical-align: middle;
}

.data-table td code {
    background: var(--gray-100, #f3f4f6);
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
    font-family: 'SF Mono', Monaco, 'Courier New', monospace;
    color: var(--primary-700, #047857);
}

.data-table tbody tr {
    transition: background 0.15s ease;
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
    gap: 8px;
    align-items: center;
}

.action-buttons form {
    margin: 0;
}

/* ===== Badges ===== */
.badge {
    display: inline-flex;
    align-items: center;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
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
    padding: 16px 24px;
    border-top: 1px solid var(--gray-200, #e5e7eb);
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

/* ===== Modern Modal ===== */
.modal {
    border: none;
    border-radius: 16px;
    padding: 0;
    max-width: 520px;
    width: 90%;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    overflow: hidden;
}

.modal::backdrop {
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
}

.modal-content {
    background: white;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid var(--gray-200, #e5e7eb);
    background: var(--gray-50, #f9fafb);
}

.modal-header h3 {
    font-size: 18px;
    font-weight: 700;
    color: var(--gray-900, #111827);
    margin: 0;
}

.modal-close {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: var(--gray-400, #9ca3af);
    border-radius: 8px;
    transition: all 0.2s ease;
}

.modal-close:hover {
    background: var(--gray-200, #e5e7eb);
    color: var(--gray-700, #374151);
}

.modal-body {
    padding: 24px;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 20px 24px;
    border-top: 1px solid var(--gray-200, #e5e7eb);
    background: var(--gray-50, #f9fafb);
}

/* ===== Detail View ===== */
.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    padding: 24px;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.detail-item label {
    font-size: 12px;
    font-weight: 600;
    color: var(--gray-500, #6b7280);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-item span {
    font-size: 15px;
    color: var(--gray-800, #1f2937);
    line-height: 1.5;
}

/* ===== Participants Section ===== */
.participants-section {
    padding: 24px;
    border-top: 1px solid var(--gray-200, #e5e7eb);
}

.participants-section h3 {
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 16px;
    color: var(--gray-900, #111827);
    display: flex;
    align-items: center;
    gap: 10px;
}

.participants-section h3 .badge {
    font-size: 11px;
}

/* ===== Form Grid Layouts ===== */
.form-container {
    padding: 24px;
}

.form-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.form-grid-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.form-actions {
    display: flex;
    gap: 12px;
    padding-top: 16px;
    margin-top: 24px;
    border-top: 1px solid var(--gray-200, #e5e7eb);
}

.form-section-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--gray-900, #111827);
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid var(--primary-500, #10b981);
    display: inline-block;
}

/* ===== Checkbox and Radio ===== */
input[type="checkbox"],
input[type="radio"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: var(--primary-500, #10b981);
}

/* ===== Modern Pagination ===== */
.pagination-wrapper {
    padding: 20px 24px;
    border-top: 1px solid var(--gray-200, #e5e7eb);
    background: var(--gray-50, #f9fafb);
}

.pagination-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
    flex-wrap: wrap;
}

.pagination-info {
    font-size: 14px;
    color: var(--gray-600, #4b5563);
}

.pagination-info .font-medium {
    font-weight: 600;
    color: var(--gray-900, #111827);
}

.pagination-numbers {
    display: flex;
    gap: 6px;
    align-items: center;
}

.pagination-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0 14px;
    border: 1px solid var(--gray-200, #e5e7eb);
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    color: var(--gray-700, #374151);
    background: white;
    text-decoration: none;
    transition: all 0.2s ease;
    cursor: pointer;
}

.pagination-btn:hover:not(.pagination-btn-disabled):not(.pagination-btn-active) {
    background: var(--primary-500, #10b981);
    border-color: var(--primary-500, #10b981);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.pagination-btn-active {
    background: linear-gradient(135deg, var(--primary-500, #10b981), var(--primary-600, #059669));
    color: white;
    border-color: var(--primary-500, #10b981);
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.pagination-btn-disabled {
    opacity: 0.4;
    cursor: not-allowed;
    pointer-events: none;
}

.pagination-btn svg {
    width: 16px;
    height: 16px;
}

.pagination-dots {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    color: var(--gray-400, #9ca3af);
    font-weight: bold;
    letter-spacing: 2px;
}

/* ===== File Upload Styling ===== */
.upload-info {
    background: var(--gray-50, #f9fafb);
    border-radius: 10px;
    padding: 16px;
    font-size: 13px;
    color: var(--gray-600, #4b5563);
    border: 1px dashed var(--gray-300, #d1d5db);
}

.upload-info p {
    margin: 0 0 8px 0;
}

.upload-info p:last-child {
    margin-bottom: 0;
}

input[type="file"].form-input {
    padding: 10px 14px;
    cursor: pointer;
}

input[type="file"].form-input::file-selector-button {
    background: linear-gradient(135deg, var(--primary-500, #10b981), var(--primary-600, #059669));
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    margin-right: 12px;
    transition: all 0.2s ease;
}

input[type="file"].form-input::file-selector-button:hover {
    background: linear-gradient(135deg, var(--primary-600, #059669), var(--primary-700, #047857));
}

/* ===== Responsive ===== */
@media (max-width: 1024px) {
    .pagination-nav {
        flex-direction: column;
        gap: 16px;
    }

    .pagination-info {
        text-align: center;
    }
}

@media (max-width: 768px) {
    .card-header {
        padding: 20px;
    }

    .card-filters {
        padding: 16px 20px;
    }

    .filter-row {
        flex-direction: column;
        align-items: stretch;
    }

    .filter-select,
    .form-input {
        min-width: 100%;
        max-width: 100%;
    }

    .date-filter {
        flex-direction: column;
        align-items: stretch;
    }

    .header-actions {
        width: 100%;
    }

    .header-actions .btn {
        flex: 1;
        justify-content: center;
    }

    .form-grid-2,
    .form-grid-3 {
        grid-template-columns: 1fr;
    }

    .modal {
        width: 95%;
        max-width: none;
        margin: 16px;
    }

    .detail-grid {
        grid-template-columns: 1fr;
    }

    .data-table th,
    .data-table td {
        padding: 12px 14px;
    }
}
</style>
@endpush
