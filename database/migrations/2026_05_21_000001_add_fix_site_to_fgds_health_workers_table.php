<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fgds_health_workers', function (Blueprint $table) {
            $table->string('fix_site')->nullable()->after('hfs');
            $table->index('fix_site');
        });
    }

    public function down(): void
    {
        Schema::table('fgds_health_workers', function (Blueprint $table) {
            $table->dropIndex(['fix_site']);
            $table->dropColumn('fix_site');
        });
    }
};
