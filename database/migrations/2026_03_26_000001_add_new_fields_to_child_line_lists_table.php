<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('child_line_lists', function (Blueprint $table) {
            $table->string('vaccinator_name')->nullable()->after('age_in_months');
            $table->string('iit_member_name')->nullable()->after('vaccinator_name');
            $table->string('iit_member_contact')->nullable()->after('iit_member_name');
            $table->string('gps_coordinates')->nullable()->after('address');
            $table->date('date_of_coverage')->nullable()->after('plan_for_coverage');
        });
    }

    public function down(): void
    {
        Schema::table('child_line_lists', function (Blueprint $table) {
            $table->dropColumn(['vaccinator_name', 'iit_member_name', 'iit_member_contact', 'gps_coordinates', 'date_of_coverage']);
        });
    }
};
