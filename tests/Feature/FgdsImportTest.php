<?php

namespace Tests\Feature;

use App\Models\FgdsCommunity;
use App\Models\FgdsHealthWorkers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/**
 * The FGDs CSV import used to map columns by position against header names that
 * no longer existed, so the app's own template imported zero rows and the
 * reasons were counted and discarded. These pin down the repaired behaviour:
 * header-name matching, per-row reporting, and a template that round-trips.
 */
class FgdsImportTest extends TestCase
{
    use RefreshDatabase;

    private function actor(): User
    {
        return User::factory()->create();
    }

    private function csvFile(array $rows, string $name = 'import.csv'): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'csv_').'.csv';
        $handle = fopen($path, 'w');
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        return new UploadedFile($path, $name, 'text/csv', null, true);
    }

    private function download(string $route): array
    {
        $response = $this->actingAs($this->actor())->get(route($route));
        $response->assertOk();

        $path = tempnam(sys_get_temp_dir(), 'dl_').'.csv';
        file_put_contents($path, $response->streamedContent());

        $handle = fopen($path, 'r');
        $rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = $row;
        }
        fclose($handle);

        return [$rows, $path];
    }

    private function import(string $route, UploadedFile $file)
    {
        return $this->actingAs($this->actor())->post(route($route), ['file' => $file]);
    }

    // -------------------------------------------------------- Round trips

    /**
     * The headline regression: the template the app hands out must import.
     * Before the fix this produced "Successfully imported 0 records."
     */
    public function test_the_community_template_imports_as_is(): void
    {
        [, $path] = $this->download('admin.fgds-community.template');

        $this->import(
            'admin.fgds-community.import',
            new UploadedFile($path, 'fgds_community_template.csv', 'text/csv', null, true)
        )->assertRedirect();

        $this->assertSame(1, FgdsCommunity::count());

        $record = FgdsCommunity::sole();
        $this->assertSame('Karachi', $record->district);
        $this->assertSame('Gujro Zone C', $record->uc);
        $this->assertSame('Govt Dispensary Bilal Colony', $record->fix_site);
        $this->assertSame('Outreach A', $record->outreach);
        $this->assertSame('Community Hall', $record->venue);
        $this->assertSame(['Mohalla One', 'Mohalla Two'], $record->community);
        $this->assertSame(6, $record->participants_males);
        $this->assertSame(4, $record->participants_females);
        $this->assertSame('2026-01-15', $record->date->format('Y-m-d'));
        // The model stamps the id when the column is left blank.
        $this->assertMatchesRegularExpression('/^FC-[A-Z0-9]{8}$/', $record->unique_id);
    }

    public function test_the_health_workers_template_imports_as_is(): void
    {
        [, $path] = $this->download('admin.fgds-health-workers.template');

        $this->import(
            'admin.fgds-health-workers.import',
            new UploadedFile($path, 'fgds_health_workers_template.csv', 'text/csv', null, true)
        )->assertRedirect();

        $record = FgdsHealthWorkers::sole();
        $this->assertSame('Gujro Zone C', $record->uc);
        $this->assertSame('Govt Dispensary Bilal Colony', $record->fix_site);
        $this->assertSame('ED Islamia', $record->hfs);
        $this->assertSame('LHW', $record->group_type);
        $this->assertMatchesRegularExpression('/^FH-[A-Z0-9]{8}$/', $record->unique_id);
    }

    /**
     * Export and import must describe the same records: an exported file, taken
     * to a clean system, has to rebuild what it came from.
     */
    public function test_an_exported_file_imports_into_a_clean_system(): void
    {
        $original = FgdsCommunity::factory()->create([
            'fix_site' => 'Saad Clinic',
            'uc' => 'Gujro Zone C',
            'district' => 'Karachi',
            'venue' => 'Community Hall',
            'community' => ['Mohalla One', 'Mohalla Two'],
        ]);
        $snapshot = $original->only(['unique_id', 'district', 'uc', 'fix_site', 'outreach', 'venue', 'facilitator_tkf']);

        [, $path] = $this->download('admin.fgds-community.export');

        // Wipe, then re-import what we exported.
        FgdsCommunity::query()->delete();
        $this->assertSame(0, FgdsCommunity::count());

        $this->import('admin.fgds-community.import', new UploadedFile($path, 'export.csv', 'text/csv', null, true))
            ->assertRedirect();

        $restored = FgdsCommunity::sole();
        foreach ($snapshot as $field => $value) {
            $this->assertSame($value, $restored->$field, "Field [{$field}] did not survive the round trip.");
        }
        $this->assertSame(['Mohalla One', 'Mohalla Two'], $restored->community);
    }

    /**
     * Re-importing an export over live data must not double every record — the
     * Form ID column identifies rows that are already present.
     */
    public function test_reimporting_an_export_skips_records_that_already_exist(): void
    {
        FgdsCommunity::factory()->count(3)->create();

        [, $path] = $this->download('admin.fgds-community.export');

        $this->import('admin.fgds-community.import', new UploadedFile($path, 'export.csv', 'text/csv', null, true))
            ->assertRedirect()
            ->assertSessionHas('success', fn ($m) => str_contains($m, 'Imported 0') && str_contains($m, 'Skipped 3'));

        $this->assertSame(3, FgdsCommunity::count());
    }

    // ---------------------------------------------------- Header handling

    public function test_columns_may_appear_in_any_order(): void
    {
        $this->import('admin.fgds-community.import', $this->csvFile([
            ['Venue', 'Community', 'Session Date', 'UC', 'Females', 'District', 'Males', 'Outreach', 'Fix Site', 'Facilitator TKF'],
            ['Community Hall', 'Mohalla One', '2026-01-15', 'Gujro Zone C', '4', 'Karachi', '6', 'Outreach A', 'Saad Clinic', 'Facilitator Name'],
        ]))->assertRedirect();

        $record = FgdsCommunity::sole();
        $this->assertSame('Saad Clinic', $record->fix_site);
        $this->assertSame('Karachi', $record->district);
        $this->assertSame('Community Hall', $record->venue);
        $this->assertSame(6, $record->participants_males);
    }

    /** Files saved from the previous template used different header spellings. */
    public function test_legacy_header_spellings_still_map(): void
    {
        $this->import('admin.fgds-community.import', $this->csvFile([
            ['district', 'uc_name', 'session_date', 'facilitator_tkf', 'venue', 'Fix Site', 'Outreach', 'Community'],
            ['Karachi', 'Gujro Zone C', '2026-01-15', 'Facilitator Name', 'Community Hall', 'Saad Clinic', 'Outreach A', 'Mohalla One'],
        ]))->assertRedirect();

        $record = FgdsCommunity::sole();
        $this->assertSame('Gujro Zone C', $record->uc);
        $this->assertSame('2026-01-15', $record->date->format('Y-m-d'));
    }

    public function test_health_workers_legacy_facility_headers_map_onto_hfs_and_group_type(): void
    {
        $this->import('admin.fgds-health-workers.import', $this->csvFile([
            ['uc_name', 'session_date', 'facilitator_tkf', 'facility_name', 'facility_type', 'Address'],
            ['Gujro Zone C', '2026-01-15', 'Facilitator Name', 'ED Islamia', 'Medics', 'Islamia Colony 1'],
        ]))->assertRedirect();

        $record = FgdsHealthWorkers::sole();
        $this->assertSame('ED Islamia', $record->hfs);
        $this->assertSame('Medics', $record->group_type);
    }

    public function test_an_unrecognisable_header_row_imports_nothing_and_says_so(): void
    {
        $this->import('admin.fgds-community.import', $this->csvFile([
            ['col1', 'col2', 'col3'],
            ['a', 'b', 'c'],
        ]))
            ->assertRedirect()
            ->assertSessionHas('error', fn ($m) => str_contains($m, 'Could not recognise'));

        $this->assertSame(0, FgdsCommunity::count());
    }

    // ------------------------------------------------------ Row reporting

    public function test_a_row_missing_required_values_is_reported_by_row_number(): void
    {
        $this->import('admin.fgds-community.import', $this->csvFile([
            ['District', 'UC', 'Fix Site', 'Outreach', 'Session Date', 'Facilitator TKF', 'Venue', 'Community'],
            ['Karachi', 'Gujro Zone C', 'Saad Clinic', 'Outreach A', '2026-01-15', 'Facilitator Name', 'Community Hall', 'Mohalla One'],
            ['Karachi', '', 'Saad Clinic', 'Outreach A', '2026-01-15', 'Facilitator Name', 'Community Hall', 'Mohalla One'],
        ]))
            ->assertRedirect()
            ->assertSessionHas('success', fn ($m) => str_contains($m, 'Imported 1') && str_contains($m, 'Skipped 1'))
            ->assertSessionHas('error', fn ($m) => str_contains($m, 'Row 3') && str_contains($m, 'UC'));

        $this->assertSame(1, FgdsCommunity::count());
    }

    public function test_an_unreadable_date_is_reported_rather_than_swallowed(): void
    {
        $this->import('admin.fgds-community.import', $this->csvFile([
            ['District', 'UC', 'Fix Site', 'Outreach', 'Session Date', 'Facilitator TKF', 'Venue', 'Community'],
            ['Karachi', 'Gujro Zone C', 'Saad Clinic', 'Outreach A', 'not a date', 'Facilitator Name', 'Community Hall', 'Mohalla One'],
        ]))
            ->assertRedirect()
            ->assertSessionHas('error', fn ($m) => str_contains($m, 'Row 2') && str_contains($m, 'session date'));

        $this->assertSame(0, FgdsCommunity::count());
    }

    public function test_trailing_blank_lines_are_ignored_rather_than_reported(): void
    {
        $this->import('admin.fgds-community.import', $this->csvFile([
            ['District', 'UC', 'Fix Site', 'Outreach', 'Session Date', 'Facilitator TKF', 'Venue', 'Community'],
            ['Karachi', 'Gujro Zone C', 'Saad Clinic', 'Outreach A', '2026-01-15', 'Facilitator Name', 'Community Hall', 'Mohalla One'],
            ['', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', ''],
        ]))
            ->assertRedirect()
            ->assertSessionHas('success', fn ($m) => str_contains($m, 'Imported 1') && ! str_contains($m, 'Skipped'));

        $this->assertSame(1, FgdsCommunity::count());
    }

    /** Fix Site is required on Community (NOT NULL) but optional on Health Workers. */
    public function test_fix_site_is_required_on_community_and_optional_on_health_workers(): void
    {
        $this->import('admin.fgds-community.import', $this->csvFile([
            ['District', 'UC', 'Fix Site', 'Outreach', 'Session Date', 'Facilitator TKF', 'Venue', 'Community'],
            ['Karachi', 'Gujro Zone C', '', 'Outreach A', '2026-01-15', 'Facilitator Name', 'Community Hall', 'Mohalla One'],
        ]))
            ->assertRedirect()
            ->assertSessionHas('error', fn ($m) => str_contains($m, 'Fix Site'));
        $this->assertSame(0, FgdsCommunity::count());

        $this->import('admin.fgds-health-workers.import', $this->csvFile([
            ['UC', 'Fix Site', 'Session Date', 'Facilitator TKF', 'HFS', 'Address', 'Group Type'],
            ['Gujro Zone C', '', '2026-01-15', 'Facilitator Name', 'ED Islamia', 'Islamia Colony 1', 'LHW'],
        ]))->assertRedirect();

        $this->assertSame(1, FgdsHealthWorkers::count());
        $this->assertNull(FgdsHealthWorkers::sole()->fix_site);
    }

    public function test_it_rejects_a_non_csv_upload(): void
    {
        $this->actingAs($this->actor())
            ->post(route('admin.fgds-community.import'), [
                'file' => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf'),
            ])
            ->assertSessionHasErrors('file');

        $this->assertSame(0, FgdsCommunity::count());
    }
}
