<?php

namespace Tests\Feature;

use App\Models\BarrierCategory;
use App\Models\FgdsCommunity;
use App\Models\FgdsCommunityBarrier;
use App\Models\OutreachSite;
use App\Models\User;
use App\Models\VaccinationRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Smoke coverage for the Reports hub: every page renders and every export
 * streams an .xlsx, both with an empty database and with seeded records.
 */
class ReportsTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create();
    }

    private const XLSX = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    public function test_all_report_pages_render_on_an_empty_database(): void
    {
        $user = $this->user();

        foreach (['reports.index', 'reports.summary', 'reports.barriers', 'reports.vaccination', 'reports.fixed-site'] as $route) {
            $this->actingAs($user)->get(route('admin.' . $route))->assertOk();
        }
    }

    public function test_all_exports_stream_a_workbook(): void
    {
        $user = $this->user();

        foreach (['reports.summary.export', 'reports.barriers.export', 'reports.vaccination.export'] as $route) {
            $response = $this->actingAs($user)->get(route('admin.' . $route));
            $response->assertOk();
            $this->assertSame(self::XLSX, $response->headers->get('content-type'));
        }
    }

    public function test_summary_and_barriers_reflect_seeded_data(): void
    {
        $user = $this->user();

        OutreachSite::create([
            'district' => 'Karachi',
            'union_council' => 'Gujro Zone C',
            'fix_site' => 'BHU Gujro',
            'outreach_site' => 'Outreach A',
        ]);

        // The canonical categories are seeded by migration; reuse one.
        $category = BarrierCategory::firstOrCreate(['name' => BarrierCategory::CANONICAL[0]]);

        $fgd = FgdsCommunity::factory()->create();
        FgdsCommunityBarrier::create([
            'fgds_community_id' => $fgd->id,
            'barrier_category_id' => $category->id,
            'barrier_text' => 'Parents refuse due to rumours',
            'serial_number' => 1,
        ]);

        VaccinationRecord::create([
            'unique_id' => 'VR-TEST0001',
            'user_id' => $user->id,
            'fix_site' => 'BHU Gujro',
            'uc' => 'Gujro Zone C',
            'district' => 'Karachi',
            'child_name' => 'Test Child',
            'father_name' => 'Test Father',
            'category' => 'Defaulter',
            'vaccinated' => 'YES',
        ]);

        // Summary: one FGD-Community session and its barrier show up.
        $this->actingAs($user)->get(route('admin.reports.summary'))
            ->assertOk()
            ->assertSee(BarrierCategory::CANONICAL[0]);

        // Barriers: the barrier text appears in the detail table.
        $this->actingAs($user)->get(route('admin.reports.barriers'))
            ->assertOk()
            ->assertSee('Parents refuse due to rumours');

        // Vaccination: the seeded record and 100% coverage show.
        $this->actingAs($user)->get(route('admin.reports.vaccination'))
            ->assertOk()
            ->assertSee('VR-TEST0001')
            ->assertSee('100%');
    }
}
