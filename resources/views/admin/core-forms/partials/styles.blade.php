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
</style>
@endpush
