<?php

namespace Tests\Feature;

use App\Models\BarrierCategory;
use App\Models\FgdsCommunity;
use App\Models\FgdsCommunityBarrier;
use App\Models\FgdsHealthWorkers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The FGDs index filters are page-wide by design: one filter drives the table,
 * the stat cards, the barrier-category cards and the map. A filter that only
 * narrowed the table would leave the counts contradicting the rows on screen,
 * so these pin down the whole page, not just the listing.
 */
class FgdsFixSiteFilterTest extends TestCase
{
    use RefreshDatabase;

    private const SITE_A = 'Govt Dispensary Bilal Colony';

    private const SITE_B = 'Sindh Govt 50 Bedded Hospital';

    private function actor(): User
    {
        return User::factory()->create();
    }

    // ---------------------------------------------------------------- Community

    private function communityRecords(): array
    {
        $a = FgdsCommunity::factory()->create(['fix_site' => self::SITE_A, 'venue' => 'Venue A']);
        $b = FgdsCommunity::factory()->create(['fix_site' => self::SITE_B, 'venue' => 'Venue B']);

        // Two participants on A, one on B — counts must follow the filter.
        foreach ([['Ali', 'Male'], ['Sana', 'Female']] as [$name, $gender]) {
            $a->participants()->create(['name' => $name, 'gender' => $gender, 'sr_no' => 1]);
        }
        $b->participants()->create(['name' => 'Bilal', 'gender' => 'Male', 'sr_no' => 1]);

        $category = BarrierCategory::where('name', 'Access Issues')->sole();
        FgdsCommunityBarrier::create([
            'fgds_community_id' => $a->id, 'barrier_category_id' => $category->id,
            'barrier_text' => 'Barrier at site A', 'serial_number' => 1,
        ]);
        FgdsCommunityBarrier::create([
            'fgds_community_id' => $b->id, 'barrier_category_id' => $category->id,
            'barrier_text' => 'Barrier at site B', 'serial_number' => 1,
        ]);

        return [$a, $b];
    }

    public function test_the_community_index_offers_every_fix_site_in_the_catalogue(): void
    {
        $this->communityRecords();

        $response = $this->actingAs($this->actor())->get(route('admin.fgds-community.index'));

        $response->assertOk();
        $response->assertSee('All Fix Sites');
        $response->assertSee(self::SITE_A);
        $response->assertSee(self::SITE_B);
    }

    public function test_the_community_fix_site_filter_narrows_the_listing(): void
    {
        [$a, $b] = $this->communityRecords();

        $response = $this->actingAs($this->actor())
            ->get(route('admin.fgds-community.index', ['fix_site' => self::SITE_A]));

        $response->assertOk();
        $response->assertSee($a->unique_id);
        $response->assertDontSee($b->unique_id);
    }

    public function test_the_community_fix_site_filter_also_drives_the_stat_cards(): void
    {
        $this->communityRecords();

        $stats = $this->actingAs($this->actor())
            ->get(route('admin.fgds-community.index', ['fix_site' => self::SITE_A]))
            ->assertOk()
            ->viewData('stats');

        $this->assertSame(1, $stats['total']);
        $this->assertSame(1, $stats['total_barriers']);
        $this->assertSame(2, $stats['total_participants']);
        $this->assertSame(1, $stats['total_males']);
        $this->assertSame(1, $stats['total_females']);
    }

    public function test_the_community_fix_site_filter_also_drives_the_map(): void
    {
        $this->communityRecords();

        $mapData = $this->actingAs($this->actor())
            ->get(route('admin.fgds-community.index', ['fix_site' => self::SITE_A]))
            ->assertOk()
            ->viewData('mapData');

        $this->assertCount(1, $mapData);
    }

    public function test_the_community_barrier_drilldown_respects_the_fix_site_filter(): void
    {
        $this->communityRecords();
        $category = BarrierCategory::where('name', 'Access Issues')->sole();

        $this->actingAs($this->actor())
            ->getJson(route('admin.fgds-community.barriers-by-category', $category).'?fix_site='.urlencode(self::SITE_A))
            ->assertOk()
            ->assertJsonPath('count', 1)
            ->assertJsonPath('records.0.barriers.0.text', 'Barrier at site A');
    }

    public function test_an_unknown_community_fix_site_yields_an_empty_page(): void
    {
        $this->communityRecords();

        $stats = $this->actingAs($this->actor())
            ->get(route('admin.fgds-community.index', ['fix_site' => 'No Such Site']))
            ->assertOk()
            ->viewData('stats');

        $this->assertSame(0, $stats['total']);
    }

    public function test_no_fix_site_filter_shows_everything(): void
    {
        $this->communityRecords();

        $stats = $this->actingAs($this->actor())
            ->get(route('admin.fgds-community.index'))
            ->assertOk()
            ->viewData('stats');

        $this->assertSame(2, $stats['total']);
    }

    // ------------------------------------------------------------ Health workers

    public function test_the_health_workers_fix_site_filter_narrows_the_page(): void
    {
        $a = FgdsHealthWorkers::factory()->create(['fix_site' => self::SITE_A]);
        $b = FgdsHealthWorkers::factory()->create(['fix_site' => self::SITE_B]);

        $response = $this->actingAs($this->actor())
            ->get(route('admin.fgds-health-workers.index', ['fix_site' => self::SITE_A]));

        $response->assertOk();
        $response->assertSee($a->unique_id);
        $response->assertDontSee($b->unique_id);
        $this->assertSame(1, $response->viewData('stats')['total']);
    }

    public function test_the_health_workers_index_offers_every_fix_site(): void
    {
        FgdsHealthWorkers::factory()->create(['fix_site' => self::SITE_A]);
        FgdsHealthWorkers::factory()->create(['fix_site' => self::SITE_B]);

        $this->actingAs($this->actor())
            ->get(route('admin.fgds-health-workers.index'))
            ->assertOk()
            ->assertSee('All Fix Sites')
            ->assertSee(self::SITE_B);
    }

    /**
     * Records without a fix site must not appear under a named site, and must
     * not break the dropdown — 6 of 19 live health-worker rows have none.
     */
    public function test_records_without_a_fix_site_are_excluded_from_a_named_filter(): void
    {
        FgdsHealthWorkers::factory()->create(['fix_site' => self::SITE_A]);
        FgdsHealthWorkers::factory()->create(['fix_site' => null]);

        $response = $this->actingAs($this->actor())
            ->get(route('admin.fgds-health-workers.index', ['fix_site' => self::SITE_A]))
            ->assertOk();

        $this->assertSame(1, $response->viewData('stats')['total']);
        $this->assertCount(1, $response->viewData('fixSites'));
    }
}
