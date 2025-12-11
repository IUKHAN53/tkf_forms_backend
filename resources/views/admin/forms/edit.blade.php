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
                    @php $hasOptions = in_array($field->type->value, ['select', 'radio', 'checkbox']); @endphp
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
                                <select name="fields[{{ $loop->index }}][type]" class="form-input field-type-select" required onchange="toggleOptionsEditor(this)">
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

                        <!-- Options editor for select, radio, checkbox -->
                        <div class="options-editor" style="{{ $hasOptions ? 'display:block;' : 'display:none;' }}">
                            <label class="form-label">Options (value : label)</label>
                            <div class="options-list">
                                @if($field->options)
                                    @foreach($field->options as $optIdx => $opt)
                                        <div class="option-row">
                                            <input type="text" name="fields[{{ $loop->parent->index }}][options][{{ $optIdx }}][value]" class="form-input" placeholder="Value" value="{{ $opt['value'] ?? '' }}" required>
                                            <input type="text" name="fields[{{ $loop->parent->index }}][options][{{ $optIdx }}][label]" class="form-input" placeholder="Label" value="{{ $opt['label'] ?? '' }}" required>
                                            <button type="button" class="btn-icon" onclick="removeOption(this)">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary" onclick="addOption(this, '{{ $loop->index }}')">+ Add Option</button>
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
                <select name="fields[${index}][type]" class="form-input field-type-select" required onchange="toggleOptionsEditor(this)">${options}</select>
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

        <!-- Options editor for select, radio, checkbox -->
        <div class="options-editor" style="display: none;">
            <label class="form-label">Options (value : label)</label>
            <div class="options-list"></div>
            <button type="button" class="btn btn-sm btn-secondary" onclick="addOption(this, '${index}')">+ Add Option</button>
        </div>
    </div>`;
}

function updateFieldNumbers() {
    document.querySelectorAll('.field-item').forEach((field, index) => {
        const badge = field.querySelector('.field-number');
        if (badge) badge.textContent = `Field ${index + 1}`;
    });
}

const typesWithOptions = ['select', 'radio', 'checkbox'];

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

    const html = `
        <div class="option-row">
            <input type="text" name="fields[${fieldIdx}][options][${optionIdx}][value]" class="form-input" placeholder="Value" required>
            <input type="text" name="fields[${fieldIdx}][options][${optionIdx}][label]" class="form-input" placeholder="Label" required>
            <button type="button" class="btn-icon" onclick="removeOption(this)">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>`;

    optionsList.insertAdjacentHTML('beforeend', html);
}

function removeOption(button) {
    button.closest('.option-row').remove();
}

updateFieldNumbers();
</script>
@endpush
