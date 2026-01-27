<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bridging_the_gap_action_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bridging_the_gap_id')->constrained('bridging_the_gaps')->cascadeOnDelete();
            $table->text('problem');
            $table->text('solution')->nullable();
            $table->text('action_needed')->nullable();
            $table->string('who_is_responsible')->nullable();
            $table->string('timeline')->nullable();
            $table->integer('serial_number')->nullable();
            $table->timestamps();

            $table->index('bridging_the_gap_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bridging_the_gap_action_plans');
    }
};
