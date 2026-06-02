<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Normalize the "Islamia Colony" union council to the canonical "Islamia Colony-09"
 * spelling in the outreach-site catalogue, so the Fixed Site Report (and every other
 * frontend that lists UCs straight from the catalogue) shows "09" instead of "-9".
 *
 * Form-data tables keep their raw spellings; those are consolidated at display time
 * via DashboardController::getConsolidatedUcName(), which already targets
 * "Islamia Colony-09".
 */
return new class extends Migration
{
    private const CANONICAL = 'Islamia Colony-09';

    private const VARIANTS = [
        'Islamia Colony-9',
        'Islamia Colony 9',
        'Islamia colony-9',
        'Islamia Colony 09',
        'Islamia colony-09',
    ];

    public function up(): void
    {
        DB::table('outreach_sites')
            ->whereIn('union_council', self::VARIANTS)
            ->update(['union_council' => self::CANONICAL]);
    }

    public function down(): void
    {
        // Restore the catalogue's previous spelling. Irreversible variant detail is
        // lost, so we fall back to the single spelling the catalogue actually used.
        DB::table('outreach_sites')
            ->where('union_council', self::CANONICAL)
            ->update(['union_council' => 'Islamia Colony-9']);
    }
};
