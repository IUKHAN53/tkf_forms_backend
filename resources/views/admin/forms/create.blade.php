@extends('layouts.admin')

@section('title', 'Create Form')
@section('page-title', 'Create New Form')

@section('content')
<form action="{{ route('admin.forms.store') }}" method="POST" id="createFormForm">
    @csrf
    
    <div class="form-grid">
        <div class="card">
            <h3 class="card-title">Form Details</h3>
            
            <div class="form-group">
                <label for="name" class="form-label">Form Name *</label>
                <input type="text" id="name" name="name" class="form-input" required value="{{ old('name') }}">
                @error('name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-input" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="version" class="form-label">Version *</label>
                    <input type="text" id="version" name="version" class="form-input" required value="{{ old('version', '1.0') }}">
                    @error('version')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_active" value="1" checked>
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
                <p class="text-secondary">Click "Add Field" to start building your form</p>
            </div>
        </div>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Create Form</button>
        <a href="{{ route('admin.forms.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<template id="fieldTemplate">
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
                <input type="text" name="fields[INDEX][label]" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Field Name *</label>
                <input type="text" name="fields[INDEX][name]" class="form-input" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Type *</label>
                <select name="fields[INDEX][type]" class="form-input field-type-select" required onchange="toggleOptionsEditor(this)">
                    @foreach($fieldTypes as $type)
                        <option value="{{ $type->value }}">{{ ucfirst($type->value) }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Order</label>
                <input type="number" name="fields[INDEX][order]" class="form-input" value="0">
            </div>
            
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="fields[INDEX][required]" value="1">
                    <span>Required</span>
                </label>
            </div>
        </div>

        <!-- Options editor for select, radio, checkbox -->
        <div class="options-editor" style="display: none;">
            <label class="form-label">Options (value : label)</label>
            <div class="options-list"></div>
            <button type="button" class="btn btn-sm btn-secondary" onclick="addOption(this, 'INDEX')">+ Add Option</button>
        </div>
    </div>
</template>

<template id="optionRowTemplate">
    <div class="option-row">
        <input type="text" name="fields[INDEX][options][OPTIDX][value]" class="form-input" placeholder="Value" required>
        <input type="text" name="fields[INDEX][options][OPTIDX][label]" class="form-input" placeholder="Label" required>
        <button type="button" class="btn-icon" onclick="removeOption(this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
</template>

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

.options-editor {
    margin-top: var(--spacing-md);
    padding-top: var(--spacing-md);
    border-top: 1px dashed var(--color-border);
}

.options-list {
    margin-bottom: var(--spacing-sm);
}

.option-row {
    display: flex;
    gap: var(--spacing-sm);
    align-items: center;
    margin-bottom: var(--spacing-sm);
}

.option-row .form-input {
    flex: 1;
}
</style>

@push('scripts')
<script>
let fieldIndex = 0;
const typesWithOptions = ['select', 'radio', 'checkbox'];

function addField() {
    const template = document.getElementById('fieldTemplate');
    const container = document.getElementById('fieldsContainer');
    
    if (container.querySelector('p')) {
        container.innerHTML = '';
    }
    
    const clone = template.content.cloneNode(true);
    const html = clone.querySelector('.field-item').outerHTML.replace(/INDEX/g, fieldIndex);
    
    container.insertAdjacentHTML('beforeend', html);
    updateFieldNumbers();
    fieldIndex++;
}

function removeField(button) {
    const container = document.getElementById('fieldsContainer');
    button.closest('.field-item').remove();
    
    if (container.children.length === 0) {
        container.innerHTML = '<p class="text-secondary">Click "Add Field" to start building your form</p>';
    }
    
    updateFieldNumbers();
}

function updateFieldNumbers() {
    const fields = document.querySelectorAll('.field-item');
    fields.forEach((field, index) => {
        field.querySelector('.field-number').textContent = `Field ${index + 1}`;
    });
}

function toggleOptionsEditor(selectEl) {
    const fieldItem = selectEl.closest('.field-item');
    const optionsEditor = fieldItem.querySelector('.options-editor');
    const selectedType = selectEl.value;

    if (typesWithOptions.includes(selectedType)) {
        optionsEditor.style.display = 'block';
        // Add one option row if none exist
        const optionsList = optionsEditor.querySelector('.options-list');
        if (optionsList.children.length === 0) {
            const fieldIdx = selectEl.name.match(/fields\[(\d+)\]/)[1];
            addOption(optionsEditor.querySelector('button'), fieldIdx);
        }
    } else {
        optionsEditor.style.display = 'none';
    }
}

function addOption(button, fieldIdx) {
    const optionsEditor = button.closest('.options-editor');
    const optionsList = optionsEditor.querySelector('.options-list');
    const optionIdx = optionsList.children.length;

    const template = document.getElementById('optionRowTemplate');
    const clone = template.content.cloneNode(true);
    let html = clone.querySelector('.option-row').outerHTML
        .replace(/INDEX/g, fieldIdx)
        .replace(/OPTIDX/g, optionIdx);

    optionsList.insertAdjacentHTML('beforeend', html);
}

function removeOption(button) {
    button.closest('.option-row').remove();
}
</script>
@endpush
@endsection
