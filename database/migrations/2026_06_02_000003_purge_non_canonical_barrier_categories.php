<?php

use App\Models\BarrierCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Enforce the canonical 11-category taxonomy on environments that already ran
 * ..._000002 with its earlier "keep legacy if it still has barriers" behaviour
 * (the live server is one of them).
 *
 * Idempotent:
 *  - Ensure all 11 canonical categories exist with sort_order 1..11.
 *  - Delete every other category. Barriers are wiped + re-uploaded against the
 *    11, so any leftover/legacy category can be removed outright. This is what
 *    guarantees "no trace of old categories" in the database.
 *
 * Single source of truth for the names: BarrierCategory::CANONICAL.
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // 1. Ensure the 11 canonical categories exist with the right order.
        foreach (BarrierCategory::CANONICAL as $i => $name) {
            $sortOrder = $i + 1;
            $existing  = DB::table('barrier_categories')->where('name', $name)->first();

            if ($existing) {
                DB::table('barrier_categories')
                    ->where('id', $existing->id)
                    ->update(['sort_order' => $sortOrder, 'updated_at' => $now]);
            } else {
                DB::table('barrier_categories')->insert([
                    'name'       => $name,
                    'sort_order' => $sortOrder,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 2. Remove anything that is not one of the 11.
        DB::table('barrier_categories')
            ->whereNotIn('name', BarrierCategory::CANONICAL)
            ->delete();
    }

    public function down(): void
    {
        // No-op: this migration only removes data that should never have existed
        // under the canonical taxonomy. There is nothing meaningful to restore.
    }
};
