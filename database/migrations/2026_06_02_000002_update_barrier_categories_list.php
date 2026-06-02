<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Replace the barrier-category taxonomy with the current 11 categories.
 *
 * Strategy (chosen to protect live data):
 *  - Upsert the 11 canonical categories with sort_order 1..11.
 *  - Drop any pre-existing category that is NOT in the new list AND has zero
 *    barriers attached (cascade delete would otherwise destroy barrier records).
 *  - A leftover old category that still has barriers is kept but pushed below the
 *    canonical 11 so the list stays ordered.
 */
return new class extends Migration
{
    private const CATEGORIES = [
        'Misconceptions and Misinformation about Vaccines',
        'Fear of Side Effects and Vaccine Safety Concerns',
        'Forceful Vaccination and Consent Issues',
        'Poor Behavior and Communication of Health Workers',
        'Lack of Community Awareness and Health Education',
        'Lack of Trust in Health System and Government',
        'Inadequate Services at Health Facility and Infrastructure',
        'Lack of Essential Community Services',
        'Access Issues',
        'Recommendations and Demands from Community',
        'Religious and Cultural Beliefs',
    ];

    private const LEGACY = [
        'Cultural Compatibility / Traditional Beliefs and Practices.',
        'Communication / Information.',
        'Service Availability.',
        'System and Procedures.',
        'Client / Provider Relations.',
        'Provider Technical Competence.',
        'Supplies and Equipment / Medicine.',
        'Place / Environment.',
    ];

    public function up(): void
    {
        $now = now();

        // 1. Upsert the 11 canonical categories with their fixed sort order.
        foreach (self::CATEGORIES as $i => $name) {
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

        // 2. Remove categories outside the new list, but only when unused.
        $stale = DB::table('barrier_categories')
            ->whereNotIn('name', self::CATEGORIES)
            ->get();

        foreach ($stale as $cat) {
            if ($this->hasBarriers($cat->id)) {
                // Keep it (would cascade-delete real barriers) but order it last.
                DB::table('barrier_categories')
                    ->where('id', $cat->id)
                    ->update(['sort_order' => 100 + (int) $cat->sort_order, 'updated_at' => $now]);
            } else {
                DB::table('barrier_categories')->where('id', $cat->id)->delete();
            }
        }
    }

    public function down(): void
    {
        $now = now();

        // Remove the 11 new categories where they carry no barrier data.
        $new = DB::table('barrier_categories')->whereIn('name', self::CATEGORIES)->get();
        foreach ($new as $cat) {
            if (!$this->hasBarriers($cat->id)) {
                DB::table('barrier_categories')->where('id', $cat->id)->delete();
            }
        }

        // Restore the original 8 default categories if they are gone.
        foreach (self::LEGACY as $i => $name) {
            $exists = DB::table('barrier_categories')->where('name', $name)->exists();
            if (!$exists) {
                DB::table('barrier_categories')->insert([
                    'name'       => $name,
                    'sort_order' => $i + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function hasBarriers(int $categoryId): bool
    {
        return DB::table('fgds_community_barriers')->where('barrier_category_id', $categoryId)->exists()
            || DB::table('fgds_health_workers_barriers')->where('barrier_category_id', $categoryId)->exists();
    }
};
