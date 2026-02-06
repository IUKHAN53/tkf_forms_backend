<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fgds_health_workers_barriers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fgds_health_workers_id')->constrained('fgds_health_workers')->cascadeOnDelete();
            $table->foreignId('barrier_category_id')->constrained('barrier_categories')->cascadeOnDelete();
            $table->text('barrier_text');
            $table->integer('serial_number')->nullable(); // Sr. No from import
            $table->timestamps();

            // Index for faster lookups (custom short name to avoid MySQL 64-char limit)
            $table->index(['fgds_health_workers_id', 'barrier_category_id'], 'fgds_hw_barriers_category_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fgds_health_workers_barriers');
    }
};
