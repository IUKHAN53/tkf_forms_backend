<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\FormField;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_active_forms(): void
    {
        Form::factory()->count(2)->create(['is_active' => true]);
        Form::factory()->create(['is_active' => false]);

        $response = $this->getJson('/api/v1/forms');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_it_shows_form_with_fields(): void
    {
        $form = Form::factory()->create();
        FormField::factory()->create([
            'form_id' => $form->id,
            'name' => 'field_one',
            'label' => 'Field One',
            'order' => 1,
        ]);

        $response = $this->getJson("/api/v1/forms/{$form->id}");

        $response->assertOk();
        $response->assertJsonPath('data.id', $form->id);
        $response->assertJsonCount(1, 'data.fields');
    }
}
