<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Form;
use App\Models\FormField;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $form = Form::factory()->create([
            'name' => 'Site Inspection',
            'description' => 'Capture site inspection details with photos and signature.',
            'version' => '1.0',
        ]);

        $fields = [
            [
                'label' => 'Location Name',
                'name' => 'location_name',
                'type' => \App\Enums\FormFieldType::Text,
                'required' => true,
                'order' => 1,
            ],
            [
                'label' => 'Inspection Date',
                'name' => 'inspection_date',
                'type' => \App\Enums\FormFieldType::Date,
                'required' => true,
                'order' => 2,
            ],
            [
                'label' => 'Status',
                'name' => 'status',
                'type' => \App\Enums\FormFieldType::Select,
                'required' => true,
                'options' => [
                    ['label' => 'Pass', 'value' => 'pass'],
                    ['label' => 'Fail', 'value' => 'fail'],
                ],
                'order' => 3,
            ],
            [
                'label' => 'Notes',
                'name' => 'notes',
                'type' => \App\Enums\FormFieldType::Textarea,
                'required' => false,
                'order' => 4,
            ],
            [
                'label' => 'Photo',
                'name' => 'photo',
                'type' => \App\Enums\FormFieldType::Image,
                'required' => false,
                'order' => 5,
            ],
            [
                'label' => 'Signature',
                'name' => 'signature',
                'type' => \App\Enums\FormFieldType::Signature,
                'required' => false,
                'order' => 6,
            ],
        ];

        collect($fields)->each(fn (array $field) => FormField::create($field + ['form_id' => $form->id]));
    }
}
