<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add a hash column for unique constraint (to avoid key length issues)
        Schema::table('outreach_sites', function (Blueprint $table) {
            $table->string('location_hash', 64)->nullable()->after('outreach_site');
        });

        // Populate the hash column for existing records
        \DB::table('outreach_sites')->orderBy('id')->chunk(100, function ($sites) {
            foreach ($sites as $site) {
                $hash = md5(
                    strtolower(trim($site->district ?? '')) . '|' .
                    strtolower(trim($site->union_council ?? '')) . '|' .
                    strtolower(trim($site->fix_site ?? '')) . '|' .
                    strtolower(trim($site->outreach_site ?? ''))
                );
                \DB::table('outreach_sites')
                    ->where('id', $site->id)
                    ->update(['location_hash' => $hash]);
            }
        });

        // Remove duplicate entries keeping only the first occurrence (lowest id)
        $duplicates = \DB::table('outreach_sites')
            ->select('location_hash', \DB::raw('MIN(id) as keep_id'))
            ->whereNotNull('location_hash')
            ->groupBy('location_hash')
            ->having(\DB::raw('COUNT(*)'), '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            \DB::table('outreach_sites')
                ->where('location_hash', $duplicate->location_hash)
                ->where('id', '!=', $duplicate->keep_id)
                ->delete();
        }

        // Add unique constraint on the hash column
        Schema::table('outreach_sites', function (Blueprint $table) {
            $table->unique('location_hash', 'outreach_sites_unique_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outreach_sites', function (Blueprint $table) {
            $table->dropUnique('outreach_sites_unique_location');
            $table->dropColumn('location_hash');
        });
    }
};
