<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('community_members', function (Blueprint $table) {
            $table->unsignedBigInteger('participant_id')->nullable()->after('id');
            $table->index('participant_id');
        });
    }

    public function down(): void
    {
        Schema::table('community_members', function (Blueprint $table) {
            $table->dropIndex(['participant_id']);
            $table->dropColumn('participant_id');
        });
    }
};
