<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FormField>
 */
class FormFieldFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form_id' => null,
            'label' => $this->faker->words(2, true),
            'name' => $this->faker->unique()->slug(2),
            'type' => \App\Enums\FormFieldType::Text,
            'required' => $this->faker->boolean(),
            'options' => null,
            'validation_rules' => null,
            'order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
