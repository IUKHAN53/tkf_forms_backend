<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormField;
use App\Enums\FormFieldType;
use Illuminate\Http\Request;
use App\Services\LogActivity;

class FormController extends Controller
{
    public function index()
    {
        $forms = Form::withCount('submissions')->latest()->paginate(15);
        return view('admin.forms.index', compact('forms'));
    }

    public function show(Form $form)
    {
        $form->load(['fields', 'submissions' => fn($q) => $q->latest()->take(50)]);
        return view('admin.forms.show', compact('form'));
    }

    public function create()
    {
        $fieldTypes = FormFieldType::cases();
        return view('admin.forms.create', compact('fieldTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'version' => 'required|string|max:50',
            'is_active' => 'boolean',
            'fields' => 'array',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.name' => 'required|string|max:255',
            'fields.*.type' => 'required|string',
            'fields.*.required' => 'boolean',
            'fields.*.options' => 'nullable|array',
            'fields.*.validation_rules' => 'nullable|array',
            'fields.*.order' => 'integer',
        ]);

        $form = Form::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'version' => $validated['version'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        foreach ($validated['fields'] ?? [] as $field) {
            FormField::create([
                'form_id' => $form->id,
                'label' => $field['label'],
                'name' => $field['name'],
                'type' => $field['type'],
                'required' => $field['required'] ?? false,
                'options' => $field['options'] ?? null,
                'validation_rules' => $field['validation_rules'] ?? null,
                'order' => $field['order'] ?? 0,
            ]);
        }

        LogActivity::record('form.created', "Created form {$form->name}", ['form_id' => $form->id]);

        return redirect()->route('admin.forms.show', $form)->with('success', 'Form created successfully');
    }

    public function edit(Form $form)
    {
        $form->load('fields');
        $fieldTypes = FormFieldType::cases();
        return view('admin.forms.edit', compact('form', 'fieldTypes'));
    }

    public function update(Request $request, Form $form)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'version' => 'required|string|max:50',
            'is_active' => 'boolean',
            'fields' => 'array',
            'fields.*.id' => 'nullable|integer|exists:form_fields,id',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.name' => 'required|string|max:255',
            'fields.*.type' => 'required|string',
            'fields.*.required' => 'boolean',
            'fields.*.options' => 'nullable|array',
            'fields.*.validation_rules' => 'nullable|array',
            'fields.*.order' => 'integer',
        ]);

        $form->update($validated);

        $keepIds = [];

        foreach ($validated['fields'] ?? [] as $field) {
            if (!empty($field['id'])) {
                $existing = FormField::where('form_id', $form->id)->find($field['id']);
                if ($existing) {
                    $existing->update([
                        'label' => $field['label'],
                        'name' => $field['name'],
                        'type' => $field['type'],
                        'required' => $field['required'] ?? false,
                        'options' => $field['options'] ?? null,
                        'validation_rules' => $field['validation_rules'] ?? null,
                        'order' => $field['order'] ?? 0,
                    ]);
                    $keepIds[] = $existing->id;
                }
            } else {
                $new = FormField::create([
                    'form_id' => $form->id,
                    'label' => $field['label'],
                    'name' => $field['name'],
                    'type' => $field['type'],
                    'required' => $field['required'] ?? false,
                    'options' => $field['options'] ?? null,
                    'validation_rules' => $field['validation_rules'] ?? null,
                    'order' => $field['order'] ?? 0,
                ]);
                $keepIds[] = $new->id;
            }
        }

        if (!empty($keepIds)) {
            $form->fields()->whereNotIn('id', $keepIds)->delete();
        } elseif (isset($validated['fields'])) {
            // If fields array was submitted empty, remove all existing fields
            $form->fields()->delete();
        }

        return redirect()->route('admin.forms.show', $form)->with('success', 'Form updated successfully');
    }

    public function destroy(Form $form)
    {
        $formName = $form->name;
        $formId = $form->id;
        $submissionsCount = $form->submissions()->count();

        $form->delete();

        LogActivity::record('form.deleted', "Deleted form '{$formName}' with {$submissionsCount} submissions", ['form_id' => $formId]);

        return redirect()->route('admin.forms.index')->with('success', 'Form and all related submissions deleted successfully');
    }
}
