<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bridging_the_gap_action_plans', function (Blueprint $table) {
            $table->text('root_cause')->nullable()->after('problem');
        });
    }

    public function down(): void
    {
        Schema::table('bridging_the_gap_action_plans', function (Blueprint $table) {
            $table->dropColumn('root_cause');
        });
    }
};
