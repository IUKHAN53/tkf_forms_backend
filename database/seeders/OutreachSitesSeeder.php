<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OutreachSitesSeeder extends Seeder
{
    public function run(): void
    {
        $sites = require database_path('data/sites.php');

        DB::table('outreach_sites')->insert($sites);
    }
}
