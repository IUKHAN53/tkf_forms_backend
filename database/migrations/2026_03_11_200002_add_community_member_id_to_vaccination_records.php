<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vaccination_records', function (Blueprint $table) {
            $table->foreignId('community_member_id')->nullable()->after('user_id')
                ->constrained('community_members')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vaccination_records', function (Blueprint $table) {
            $table->dropForeign(['community_member_id']);
            $table->dropColumn('community_member_id');
        });
    }
};
