<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Repairs the file columns on the core-forms tables for fresh installs.
 *
 * 2025_01_07_000001_add_file_columns_to_core_forms_tables is dated before the
 * migrations that CREATE those tables (2025_12_15 / 2026_01_07), and it guards
 * every change with Schema::hasTable(). On an existing database it worked,
 * because the tables were already there by the time it was run. On a fresh
 * database every guard is false, so it silently adds nothing and the barrier /
 * action-plan uploads fail with "no such column".
 *
 * This migration adds the same columns idempotently at a point in the ordering
 * where the tables definitely exist: a no-op on existing databases, the actual
 * fix on new ones.
 */
return new class extends Migration
{
    /**
     * table => [column, column-it-should-follow]
     */
    private const COLUMNS = [
        'bridging_the_gaps' => ['action_plan_file', 'submitted_at'],
        'fgds_community' => ['barriers_file', 'submitted_at'],
        'fgds_health_workers' => ['barriers_file', 'submitted_at'],
    ];

    public function up(): void
    {
        foreach (self::COLUMNS as $table => [$column, $after]) {
            if (! Schema::hasTable($table) || Schema::hasColumn($table, $column)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table, $column, $after) {
                $definition = $blueprint->string($column)->nullable();

                if (Schema::hasColumn($table, $after)) {
                    $definition->after($after);
                }
            });
        }
    }

    public function down(): void
    {
        // Intentionally irreversible: the sibling 2025_01_07 migration already
        // claims these columns on databases where it took effect, so dropping
        // them here would fight its down().
    }
};
