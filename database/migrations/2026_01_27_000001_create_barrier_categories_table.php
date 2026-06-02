<?php

use App\Models\BarrierCategory;
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

        // Seed the canonical 11 vaccine-specific categories (single source of
        // truth: BarrierCategory::CANONICAL). Migration ..._000002 keeps an
        // already-populated table in sync; this covers fresh installs so the old
        // taxonomy is never created.
        $now = now();
        foreach (BarrierCategory::CANONICAL as $i => $name) {
            DB::table('barrier_categories')->insert([
                'name' => $name,
                'sort_order' => $i + 1,
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
