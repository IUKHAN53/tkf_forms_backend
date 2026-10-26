<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Implementation tracking for action-plan items: a status (not_started /
 * in_progress / completed), free-text remarks from follow-up meetings, and when
 * the status last changed.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bridging_the_gap_action_plans', function (Blueprint $table) {
            $table->string('implementation_status', 20)->default('not_started')->after('timeline');
            $table->text('implementation_remarks')->nullable()->after('implementation_status');
            $table->timestamp('status_updated_at')->nullable()->after('implementation_remarks');

            $table->index('implementation_status');
        });
    }

    public function down(): void
    {
        Schema::table('bridging_the_gap_action_plans', function (Blueprint $table) {
            $table->dropIndex(['implementation_status']);
            $table->dropColumn(['implementation_status', 'implementation_remarks', 'status_updated_at']);
        });
    }
};
