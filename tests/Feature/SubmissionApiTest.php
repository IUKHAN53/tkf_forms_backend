<?php

namespace Tests\Feature;

use App\Enums\FormFieldType;
use App\Models\Form;
use App\Models\FormField;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubmissionApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_authenticated_user_can_submit_form(): void
    {
        $user = User::factory()->create();
        $form = Form::factory()->create();
        FormField::factory()->create([
            'form_id' => $form->id,
            'type' => FormFieldType::Text,
            'name' => 'title',
            'label' => 'Title',
            'required' => true,
        ]);

        $payload = [
            'data' => [
                'title' => 'Example submission',
            ],
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/v1/forms/{$form->id}/submit", $payload);

        $response->assertCreated();
        $this->assertDatabaseHas('form_submissions', [
            'form_id' => $form->id,
            'user_id' => $user->id,
        ]);
    }
}
