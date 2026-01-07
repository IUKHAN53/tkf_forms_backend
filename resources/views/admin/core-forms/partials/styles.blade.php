@push('styles')
<style>
.content-card {
    background: var(--color-bg-paper);
    border-radius: var(--radius-lg);
    border: 1px solid var(--color-border);
    overflow: hidden;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: var(--spacing-lg);
    border-bottom: 1px solid var(--color-border);
    gap: var(--spacing-md);
    flex-wrap: wrap;
}

.header-left h2 {
    font-size: 20px;
    font-weight: 700;
    color: var(--color-text-primary);
    margin-bottom: 4px;
}

.header-actions {
    display: flex;
    gap: var(--spacing-sm);
    flex-wrap: wrap;
}

.card-filters {
    padding: var(--spacing-md) var(--spacing-lg);
    background: var(--color-bg-neutral);
    border-bottom: 1px solid var(--color-border);
}

.search-form {
    display: flex;
    gap: var(--spacing-sm);
    flex-wrap: wrap;
}

/* Advanced Filter Styles */
.filter-form {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.filter-row {
    display: flex;
    gap: var(--spacing-sm);
    flex-wrap: wrap;
    align-items: center;
}

.filter-select {
    min-width: 150px;
    max-width: 200px;
}

.date-filter {
    display: flex;
    align-items: center;
    gap: 8px;
}

.date-filter label {
    font-size: 13px;
    font-weight: 500;
    color: var(--color-text-secondary);
    white-space: nowrap;
}

.date-filter .form-input {
    min-width: 140px;
}

.form-input {
    padding: 10px 14px;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-sm);
    font-size: 14px;
    min-width: 250px;
    background: var(--color-bg-paper);
    color: var(--color-text-primary);
}

.table-container {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th,
.data-table td {
    padding: 12px 16px;
    text-align: left;
    border-bottom: 1px solid var(--color-border);
}

.data-table th {
    background: var(--color-bg-neutral);
    font-weight: 600;
    color: var(--color-text-secondary);
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.data-table td {
    color: var(--color-text-primary);
    font-size: 14px;
}

.data-table tbody tr:hover {
    background: var(--color-bg-neutral);
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 16px;
    border-radius: var(--radius-sm);
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    text-decoration: none;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

.btn-primary {
    background: var(--color-primary-main);
    color: white;
}

.btn-primary:hover {
    background: var(--color-primary-dark);
}

.btn-outline {
    background: transparent;
    border: 1px solid var(--color-border);
    color: var(--color-text-primary);
}

.btn-outline:hover {
    background: var(--color-bg-neutral);
}

.btn-danger {
    background: var(--color-error-main);
    color: white;
}

.btn-danger:hover {
    background: var(--color-error-dark);
}

.btn-success {
    background: linear-gradient(135deg, var(--color-success-main), var(--color-success-dark));
    color: white;
    border-color: var(--color-success-main);
}

.btn-success:hover {
    background: linear-gradient(135deg, var(--color-success-dark), var(--color-success-darker));
}

.card-footer {
    padding: var(--spacing-md) var(--spacing-lg);
    border-top: 1px solid var(--color-border);
}

.text-center {
    text-align: center;
}

.text-muted {
    color: var(--color-text-secondary);
}

.mb-md {
    margin-bottom: var(--spacing-md);
}

/* Modal */
.modal {
    border: none;
    border-radius: var(--radius-lg);
    padding: 0;
    max-width: 500px;
    width: 90%;
    box-shadow: var(--shadow-dropdown);
}

.modal::backdrop {
    background: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background: var(--color-bg-paper);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--spacing-lg);
    border-bottom: 1px solid var(--color-border);
}

.modal-header h3 {
    font-size: 18px;
    font-weight: 700;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: var(--color-text-secondary);
}

.modal-body {
    padding: var(--spacing-lg);
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: var(--spacing-sm);
    padding: var(--spacing-lg);
    border-top: 1px solid var(--color-border);
}

/* Detail view */
.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: var(--spacing-lg);
    padding: var(--spacing-lg);
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.detail-item label {
    font-size: 12px;
    font-weight: 600;
    color: var(--color-text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-item span {
    font-size: 15px;
    color: var(--color-text-primary);
}

/* Participants table */
.participants-section {
    padding: var(--spacing-lg);
    border-top: 1px solid var(--color-border);
}

.participants-section h3 {
    font-size: 16px;
    font-weight: 700;
    margin-bottom: var(--spacing-md);
    color: var(--color-text-primary);
}

/* Badge */
.badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.badge-primary {
    background: var(--color-primary-lighter);
    color: var(--color-primary-darker);
}

.badge-success {
    background: var(--color-success-lighter);
    color: var(--color-success-darker);
}

.badge-danger {
    background: var(--color-error-lighter);
    color: var(--color-error-darker);
}

.badge-warning {
    background: #fef3c7;
    color: #92400e;
}

/* Modern Form Styling */
.form-container {
    padding: var(--spacing-lg);
}

.form-group {
    margin-bottom: 24px;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--color-text-primary);
    margin-bottom: 8px;
    letter-spacing: 0.2px;
}

.form-input,
.form-select,
textarea.form-input,
select.form-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--color-border);
    border-radius: 8px;
    font-size: 14px;
    background: var(--color-bg-paper);
    color: var(--color-text-primary);
    transition: all 0.2s ease;
    font-family: inherit;
}

.form-input:focus,
.form-select:focus,
textarea.form-input:focus,
select.form-input:focus {
    outline: none;
    border-color: var(--color-primary-main);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input::placeholder {
    color: var(--color-text-tertiary);
}

textarea.form-input {
    resize: vertical;
    min-height: 80px;
}

.form-error {
    display: block;
    margin-top: 6px;
    font-size: 13px;
    color: var(--color-error-main);
}

.form-actions {
    display: flex;
    gap: 12px;
    padding-top: 8px;
    margin-top: 32px;
    border-top: 1px solid var(--color-border);
}

/* Enhanced button styles */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 2px solid transparent;
    text-decoration: none;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.btn:active {
    transform: translateY(0);
}

.btn-primary {
    background: linear-gradient(135deg, var(--color-primary-main), var(--color-primary-dark));
    color: white;
    border-color: var(--color-primary-main);
}

.btn-primary:hover {
    background: linear-gradient(135deg, var(--color-primary-dark), var(--color-primary-darker));
}

.btn-outline {
    background: var(--color-bg-paper);
    border: 2px solid var(--color-border);
    color: var(--color-text-primary);
    box-shadow: none;
}

.btn-outline:hover {
    background: var(--color-bg-neutral);
    border-color: var(--color-text-tertiary);
}

.btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    padding: 0;
    border-radius: 6px;
    background: transparent;
    border: 1px solid var(--color-border);
    color: var(--color-text-secondary);
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-icon:hover {
    background: var(--color-bg-neutral);
    color: var(--color-primary-main);
    border-color: var(--color-primary-main);
}

/* Section headers in forms */
.form-section-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--color-text-primary);
    margin-bottom: 16px;
    padding-bottom: 8px;
    border-bottom: 2px solid var(--color-primary-main);
    display: inline-block;
}

/* Grid layouts for forms */
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

@media (max-width: 768px) {
    .form-grid-2,
    .form-grid-3 {
        grid-template-columns: 1fr;
    }
}

/* Checkbox and Radio styling */
input[type="checkbox"],
input[type="radio"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: var(--color-primary-main);
}

/* Enhanced card header for forms */
.card-header {
    background: linear-gradient(135deg, var(--color-bg-neutral), var(--color-bg-paper));
    border-bottom: 2px solid var(--color-primary-main);
}

/* Improved content card */
.content-card {
    background: var(--color-bg-paper);
    border-radius: 12px;
    border: 1px solid var(--color-border);
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

/* Select dropdown improvements */
select.form-input {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 40px;
}

/* Import Modal Improvements */
.import-modal {
    border: none;
    border-radius: 12px;
    padding: 0;
    max-width: 500px;
    width: 90%;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.import-modal::backdrop {
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
}

.close-btn {
    background: none;
    border: none;
    font-size: 28px;
    cursor: pointer;
    color: var(--color-text-secondary);
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    transition: all 0.2s;
}

.close-btn:hover {
    background: var(--color-bg-neutral);
    color: var(--color-text-primary);
}

/* Pagination Styling */
.pagination-wrapper {
    padding: var(--spacing-lg);
    border-top: 1px solid var(--color-border);
    background: var(--color-bg-neutral);
}

.pagination-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
}

.pagination-info {
    flex: 1;
    text-align: left;
}

.pagination-info p {
    font-size: 14px;
    color: var(--color-text-secondary);
    margin: 0;
    white-space: nowrap;
}

.pagination-info .font-medium {
    font-weight: 600;
    color: var(--color-text-primary);
}

.pagination-numbers {
    display: flex;
    gap: 4px;
    align-items: center;
    flex-wrap: wrap;
    justify-content: center;
    flex: 1;
}

.pagination-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0 12px;
    border: 2px solid var(--color-border);
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    color: var(--color-text-primary);
    background: var(--color-bg-paper);
    text-decoration: none;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    cursor: pointer;
}

.pagination-btn svg {
    width: 16px !important;
    height: 16px !important;
    min-width: 16px !important;
    min-height: 16px !important;
    max-width: 16px !important;
    max-height: 16px !important;
    flex-shrink: 0;
}

.pagination-controls svg {
    width: 16px !important;
    height: 16px !important;
    min-width: 16px !important;
    min-height: 16px !important;
    max-width: 16px !important;
    max-height: 16px !important;
}

.pagination-btn:hover:not(.pagination-btn-disabled):not(.pagination-btn-active) {
    background: var(--color-primary-main);
    border-color: var(--color-primary-main);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(59, 130, 246, 0.2);
}

.pagination-btn-active {
    background: linear-gradient(135deg, var(--color-primary-main), var(--color-primary-dark));
    color: white;
    border-color: var(--color-primary-main);
    font-weight: 600;
    box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
}

.pagination-btn-disabled {
    opacity: 0.4;
    cursor: not-allowed;
    pointer-events: none;
}

.pagination-dots {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    color: var(--color-text-secondary);
    font-weight: bold;
    letter-spacing: 2px;
}

@media (max-width: 1024px) {
    .pagination-nav {
        flex-wrap: wrap;
    }
    
    .pagination-info {
        order: 1;
        flex: 1 1 100%;
        text-align: center;
        margin-bottom: 12px;
    }
    
    .pagination-controls {
        order: 2;
    }
    
    .pagination-numbers {
        order: 3;
    }
}

@media (max-width: 768px) {
    .pagination-nav {
        flex-direction: column;
        gap: 16px;
    }
    
    .pagination-numbers {
        justify-content: center;
    }
}
</style>
@endpush
