<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // IIT Team Members - references participants from Community Barriers and Healthcare Barriers
        Schema::create('bridging_the_gap_team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bridging_the_gap_id')->constrained('bridging_the_gaps')->cascadeOnDelete();
            $table->foreignId('participant_id')->constrained('participants')->cascadeOnDelete();
            $table->string('source_type'); // 'community_barrier' or 'healthcare_barrier'
            $table->unsignedBigInteger('source_id'); // The ID of the source form
            $table->timestamps();

            // Composite unique constraint to prevent duplicate selections
            $table->unique(['bridging_the_gap_id', 'participant_id'], 'btg_participant_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bridging_the_gap_team_members');
    }
};
