<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('community_barriers', function (Blueprint $table) {
            $table->dropColumn('group_type');
        });
    }

    public function down(): void
    {
        Schema::table('community_barriers', function (Blueprint $table) {
            $table->json('group_type')->nullable();
        });
    }
};

