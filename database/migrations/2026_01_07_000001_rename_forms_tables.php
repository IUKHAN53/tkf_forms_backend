<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Rename community_barriers to fgds_community
        Schema::rename('community_barriers', 'fgds_community');

        // Rename healthcare_barriers to fgds_health_workers
        Schema::rename('healthcare_barriers', 'fgds_health_workers');

        // Rename draft_lists to child_line_lists
        Schema::rename('draft_lists', 'child_line_lists');

        // Update polymorphic references in participants table
        DB::table('participants')
            ->where('participantable_type', 'App\\Models\\CommunityBarrier')
            ->update(['participantable_type' => 'App\\Models\\FgdsCommunity']);

        DB::table('participants')
            ->where('participantable_type', 'App\\Models\\HealthcareBarrier')
            ->update(['participantable_type' => 'App\\Models\\FgdsHealthWorkers']);

        // Update bridging_the_gap_team_members source_type references
        DB::table('bridging_the_gap_team_members')
            ->where('source_type', 'community_barrier')
            ->update(['source_type' => 'fgds_community']);

        DB::table('bridging_the_gap_team_members')
            ->where('source_type', 'healthcare_barrier')
            ->update(['source_type' => 'fgds_health_workers']);
    }

    public function down(): void
    {
        // Revert bridging_the_gap_team_members source_type references
        DB::table('bridging_the_gap_team_members')
            ->where('source_type', 'fgds_community')
            ->update(['source_type' => 'community_barrier']);

        DB::table('bridging_the_gap_team_members')
            ->where('source_type', 'fgds_health_workers')
            ->update(['source_type' => 'healthcare_barrier']);

        // Revert polymorphic references in participants table
        DB::table('participants')
            ->where('participantable_type', 'App\\Models\\FgdsCommunity')
            ->update(['participantable_type' => 'App\\Models\\CommunityBarrier']);

        DB::table('participants')
            ->where('participantable_type', 'App\\Models\\FgdsHealthWorkers')
            ->update(['participantable_type' => 'App\\Models\\HealthcareBarrier']);

        // Rename tables back
        Schema::rename('fgds_community', 'community_barriers');
        Schema::rename('fgds_health_workers', 'healthcare_barriers');
        Schema::rename('child_line_lists', 'draft_lists');
    }
};
