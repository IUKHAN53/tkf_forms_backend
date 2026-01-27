<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barrier_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insert the 8 fixed categories
        $categories = [
            ['name' => 'Cultural Compatibility / Traditional Beliefs and Practices.', 'sort_order' => 1],
            ['name' => 'Communication / Information.', 'sort_order' => 2],
            ['name' => 'Service Availability.', 'sort_order' => 3],
            ['name' => 'System and Procedures.', 'sort_order' => 4],
            ['name' => 'Client / Provider Relations.', 'sort_order' => 5],
            ['name' => 'Provider Technical Competence.', 'sort_order' => 6],
            ['name' => 'Supplies and Equipment / Medicine.', 'sort_order' => 7],
            ['name' => 'Place / Environment.', 'sort_order' => 8],
        ];

        $now = now();
        foreach ($categories as $category) {
            DB::table('barrier_categories')->insert([
                'name' => $category['name'],
                'sort_order' => $category['sort_order'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('barrier_categories');
    }
};
