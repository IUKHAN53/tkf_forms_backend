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
 * The FGDs CSV exports previously listed columns that no longer exist on the
 * models (uc_name, session_date, epi_focal_person, barriers_identified,
 * solutions_proposed, follow_up_actions), so those cells came out blank on
 * every row. These pin the export to fields that actually resolve.
 */
class FgdsExportTest extends TestCase
{
    use RefreshDatabase;

    /** Parse the streamed CSV into a header row plus associative data rows. */
    private function csv(string $body): array
    {
        $handle = fopen('php://memory', 'r+');
        fwrite($handle, $body);
        rewind($handle);

        $rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = $row;
        }
        fclose($handle);

        $header = array_shift($rows);

        return [$header, array_map(fn ($r) => array_combine($header, $r), $rows)];
    }

    private function export(string $route): array
    {
        $response = $this->actingAs(User::factory()->create())->get(route($route));
        $response->assertOk();

        return $this->csv($response->streamedContent());
    }

    public function test_the_community_export_includes_the_fix_site(): void
    {
        FgdsCommunity::factory()->create([
            'fix_site' => 'Govt Dispensary Bilal Colony',
            'uc' => 'Gujro Zone C',
            'venue' => 'Community Hall',
        ]);

        [$header, $rows] = $this->export('admin.fgds-community.export');

        $this->assertContains('Fix Site', $header);
        $this->assertSame('Govt Dispensary Bilal Colony', $rows[0]['Fix Site']);
    }

    public function test_the_community_export_resolves_every_column_it_advertises(): void
    {
        $record = FgdsCommunity::factory()->create([
            'fix_site' => 'Govt Dispensary Bilal Colony',
            'uc' => 'Gujro Zone C',
            'district' => 'Karachi',
            'venue' => 'Community Hall',
            'facilitator_tkf' => 'Facilitator Name',
        ]);
        $record->participants()->create(['name' => 'Ali', 'gender' => 'Male', 'sr_no' => 1]);
        $record->participants()->create(['name' => 'Sana', 'gender' => 'Female', 'sr_no' => 2]);
        FgdsCommunityBarrier::create([
            'fgds_community_id' => $record->id,
            'barrier_category_id' => BarrierCategory::where('name', 'Access Issues')->sole()->id,
            'barrier_text' => 'Too far to walk',
            'serial_number' => 1,
        ]);

        [$header, $rows] = $this->export('admin.fgds-community.export');
        $row = $rows[0];

        $this->assertSame($record->unique_id, $row['Form ID']);
        $this->assertSame('Karachi', $row['District']);
        $this->assertSame('Gujro Zone C', $row['UC']);
        $this->assertSame('Community Hall', $row['Venue']);
        $this->assertSame('Facilitator Name', $row['Facilitator TKF']);
        $this->assertSame('1', $row['Barriers Identified']);
        $this->assertSame('2', $row['Participants Count']);
        $this->assertSame('1', $row['Males']);
        $this->assertSame('1', $row['Females']);
        $this->assertSame($record->date->format('Y-m-d'), $row['Session Date']);

        // Nothing may be blank across the board any more.
        foreach ($header as $column) {
            $this->assertNotSame('', $row[$column], "Column [{$column}] exported empty.");
        }
    }

    public function test_the_community_export_flattens_the_community_array(): void
    {
        FgdsCommunity::factory()->create(['community' => ['Mohalla One', 'Mohalla Two']]);

        [, $rows] = $this->export('admin.fgds-community.export');

        $this->assertSame('Mohalla One, Mohalla Two', $rows[0]['Community']);
    }

    public function test_the_health_workers_export_includes_the_fix_site(): void
    {
        FgdsHealthWorkers::factory()->create(['fix_site' => 'Saad Clinic', 'uc' => 'Gujro Zone C']);

        [$header, $rows] = $this->export('admin.fgds-health-workers.export');

        $this->assertContains('Fix Site', $header);
        $this->assertSame('Saad Clinic', $rows[0]['Fix Site']);
        $this->assertSame('Gujro Zone C', $rows[0]['UC']);
    }

    public function test_the_health_workers_export_resolves_its_columns(): void
    {
        $record = FgdsHealthWorkers::factory()->create([
            'fix_site' => 'Saad Clinic',
            'hfs' => 'Govt Dispensary Bilal Colony',
            'group_type' => 'LHW',
            'address' => 'Some Street',
        ]);

        [, $rows] = $this->export('admin.fgds-health-workers.export');
        $row = $rows[0];

        $this->assertSame($record->unique_id, $row['Form ID']);
        $this->assertSame('Govt Dispensary Bilal Colony', $row['HFS']);
        $this->assertSame('LHW', $row['Group Type']);
        $this->assertSame('Some Street', $row['Address']);
        $this->assertSame('0', $row['Barriers Identified']);
    }

    /** A record with no fix site must export an empty cell, not crash. */
    public function test_a_null_fix_site_exports_as_blank(): void
    {
        FgdsHealthWorkers::factory()->create(['fix_site' => null]);

        [, $rows] = $this->export('admin.fgds-health-workers.export');

        $this->assertSame('', $rows[0]['Fix Site']);
    }
}
