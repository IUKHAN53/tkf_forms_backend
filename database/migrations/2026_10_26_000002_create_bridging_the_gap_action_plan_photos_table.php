<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Supporting activity pictures — evidence that an action-plan item was
 * implemented. Files live on the private `local` disk and are served through an
 * authenticated admin route, never from a public URL.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bridging_the_gap_action_plan_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('action_plan_id')
                ->constrained('bridging_the_gap_action_plans')
                ->cascadeOnDelete();
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedInteger('size')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bridging_the_gap_action_plan_photos');
    }
};
