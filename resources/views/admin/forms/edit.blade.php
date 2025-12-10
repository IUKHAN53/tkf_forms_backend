@extends('layouts.admin')

@section('title', 'Edit Form')
@section('page-title', 'Edit Form: ' . $form->name)

@section('content')
<form action="{{ route('admin.forms.update', $form) }}" method="POST" id="editFormForm">
    @csrf
    @method('PUT')
    
    <div class="form-grid">
        <div class="card">
            <h3 class="card-title">Form Details</h3>
            
            <div class="form-group">
                <label for="name" class="form-label">Form Name *</label>
                <input type="text" id="name" name="name" class="form-input" required value="{{ old('name', $form->name) }}">
                @error('name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-input" rows="3">{{ old('description', $form->description) }}</textarea>
                @error('description')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="version" class="form-label">Version *</label>
                    <input type="text" id="version" name="version" class="form-input" required value="{{ old('version', $form->version) }}">
                    @error('version')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $form->is_active) ? 'checked' : '' }}>
                        <span>Active</span>
                    </label>
                </div>
            </div>
        </div>
        
        <div class="card full-width">
            <div class="card-header">
                <h3 class="card-title">Form Fields</h3>
                <button type="button" class="btn btn-sm btn-primary" onclick="addField()">Add Field</button>
            </div>

            <div id="fieldsContainer">
                @foreach($form->fields as $field)
                    <div class="field-item" data-existing="true">
                        <div class="field-header">
                            <span class="field-number"></span>
                            <button type="button" class="btn-icon" onclick="removeField(this)">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>

                        <input type="hidden" name="fields[{{ $loop->index }}][id]" value="{{ $field->id }}">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Label *</label>
                                <input type="text" name="fields[{{ $loop->index }}][label]" class="form-input" required value="{{ $field->label }}">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Field Name *</label>
                                <input type="text" name="fields[{{ $loop->index }}][name]" class="form-input" required value="{{ $field->name }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Type *</label>
                                <select name="fields[{{ $loop->index }}][type]" class="form-input" required>
                                    @foreach($fieldTypes as $type)
                                        <option value="{{ $type->value }}" @selected($field->type->value === $type->value)>{{ ucfirst($type->value) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Order</label>
                                <input type="number" name="fields[{{ $loop->index }}][order]" class="form-input" value="{{ $field->order }}">
                            </div>

                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="fields[{{ $loop->index }}][required]" value="1" {{ $field->required ? 'checked' : '' }}>
                                    <span>Required</span>
                                </label>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if($form->fields->isEmpty())
                    <p class="text-secondary">Click "Add Field" to start editing fields</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Update Form</button>
        <a href="{{ route('admin.forms.show', $form) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<style>
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: var(--spacing-lg);
    margin-bottom: var(--spacing-lg);
}

.full-width {
    grid-column: 1 / -1;
}

.form-group {
    margin-bottom: var(--spacing-md);
}

.form-label {
    display: block;
    font-weight: 600;
    color: var(--color-text-primary);
    margin-bottom: var(--spacing-xs);
    font-size: 14px;
}

.form-input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-sm);
    font-size: 15px;
    color: var(--color-text-primary);
    background-color: var(--color-bg-paper);
    transition: border-color 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: var(--color-primary-main);
}

.form-error {
    display: block;
    color: var(--color-error-main);
    font-size: 13px;
    margin-top: var(--spacing-xs);
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: var(--spacing-md);
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    cursor: pointer;
    margin-top: 28px;
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.form-actions {
    display: flex;
    gap: var(--spacing-md);
    padding-top: var(--spacing-lg);
    border-top: 1px solid var(--color-border);
}

.card-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--color-text-primary);
    margin-bottom: var(--spacing-md);
}

code {
    background-color: var(--color-bg-neutral);
    padding: 2px 6px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    font-size: 13px;
}

.field-item {
    background-color: var(--color-bg-neutral);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-sm);
    padding: var(--spacing-md);
    margin-bottom: var(--spacing-md);
}

.field-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-md);
}

.field-number {
    font-weight: 700;
    color: var(--color-text-primary);
}

.btn-icon {
    background: none;
    border: none;
    cursor: pointer;
    color: var(--color-error-main);
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-icon:hover {
    opacity: 0.7;
}
</style>
@endsection

@push('scripts')
<script>
let fieldIndex = Number('{{ $form->fields->count() }}');

function addField() {
    const container = document.getElementById('fieldsContainer');
    if (container.querySelector('p')) {
        container.innerHTML = '';
    }

    const html = renderField(fieldIndex);
    container.insertAdjacentHTML('beforeend', html);
    updateFieldNumbers();
    fieldIndex++;
}

function removeField(button) {
    const container = document.getElementById('fieldsContainer');
    button.closest('.field-item').remove();

    if (container.children.length === 0) {
        container.innerHTML = '<p class="text-secondary">Click "Add Field" to start editing fields</p>';
    }

    updateFieldNumbers();
}

function renderField(index) {
    const options = `@foreach($fieldTypes as $type)<option value="{{ $type->value }}">{{ ucfirst($type->value) }}</option>@endforeach`;

    return `
    <div class="field-item">
        <div class="field-header">
            <span class="field-number"></span>
            <button type="button" class="btn-icon" onclick="removeField(this)">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Label *</label>
                <input type="text" name="fields[${index}][label]" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label">Field Name *</label>
                <input type="text" name="fields[${index}][name]" class="form-input" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Type *</label>
                <select name="fields[${index}][type]" class="form-input" required>${options}</select>
            </div>

            <div class="form-group">
                <label class="form-label">Order</label>
                <input type="number" name="fields[${index}][order]" class="form-input" value="0">
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="fields[${index}][required]" value="1">
                    <span>Required</span>
                </label>
            </div>
        </div>
    </div>`;
}

function updateFieldNumbers() {
    document.querySelectorAll('.field-item').forEach((field, index) => {
        const badge = field.querySelector('.field-number');
        if (badge) badge.textContent = `Field ${index + 1}`;
    });
}

updateFieldNumbers();
</script>
@endpush
