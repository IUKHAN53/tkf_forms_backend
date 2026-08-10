<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds "Sub Cause" to action plans, sitting between Problem and Root Cause in
 * the canonical spreadsheet layout:
 *
 *   Problem | Sub Cause | Root Cause | Solution | Action Needed | Responsible | Timeline
 *
 * Nullable: existing rows (and any file uploaded without the column) simply
 * leave it empty.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bridging_the_gap_action_plans', function (Blueprint $table) {
            $table->text('sub_cause')->nullable()->after('problem');
        });
    }

    public function down(): void
    {
        Schema::table('bridging_the_gap_action_plans', function (Blueprint $table) {
            $table->dropColumn('sub_cause');
        });
    }
};
