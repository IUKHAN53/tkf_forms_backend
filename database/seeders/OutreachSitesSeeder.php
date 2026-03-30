<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OutreachSitesSeeder extends Seeder
{
    public function run(): void
    {
        $sites = require database_path('data/sites.php');

        foreach (array_chunk($sites, 50) as $chunk) {
            DB::table('outreach_sites')->upsert(
                $chunk,
                ['location_hash'],
                ['district', 'union_council', 'fix_site', 'outreach_site', 'coordinates', 'comments']
            );
        }
    }
}
