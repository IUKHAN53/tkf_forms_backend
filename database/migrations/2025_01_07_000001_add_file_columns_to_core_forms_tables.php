<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add action_plan_file to bridging_the_gaps
        if (Schema::hasTable('bridging_the_gaps')) {
            Schema::table('bridging_the_gaps', function (Blueprint $table) {
                $table->string('action_plan_file')->nullable()->after('submitted_at');
            });
        }

        // Add barriers_file to fgds_community
        if (Schema::hasTable('fgds_community')) {
            Schema::table('fgds_community', function (Blueprint $table) {
                $table->string('barriers_file')->nullable()->after('submitted_at');
            });
        }

        // Add barriers_file to fgds_health_workers
        if (Schema::hasTable('fgds_health_workers')) {
            Schema::table('fgds_health_workers', function (Blueprint $table) {
                $table->string('barriers_file')->nullable()->after('submitted_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('bridging_the_gaps')) {
            Schema::table('bridging_the_gaps', function (Blueprint $table) {
                $table->dropColumn('action_plan_file');
            });
        }

        if (Schema::hasTable('fgds_community')) {
            Schema::table('fgds_community', function (Blueprint $table) {
                $table->dropColumn('barriers_file');
            });
        }

        if (Schema::hasTable('fgds_health_workers')) {
            Schema::table('fgds_health_workers', function (Blueprint $table) {
                $table->dropColumn('barriers_file');
            });
        }
    }
};
