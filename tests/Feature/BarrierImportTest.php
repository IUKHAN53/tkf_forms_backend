<?php

namespace Tests\Feature;

use App\Models\BarrierCategory;
use App\Models\FgdsCommunity;
use App\Models\FgdsCommunityBarrier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

/**
 * End-to-end cover for the FGDs barrier upload: an arbitrary spreadsheet must
 * land entirely inside the canonical 11 categories, and re-uploading must
 * replace rather than accumulate.
 */
class BarrierImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    private function record(): FgdsCommunity
    {
        return FgdsCommunity::factory()->create();
    }

    private function xlsx(array $rows): UploadedFile
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->fromArray($rows, null, 'A1');

        $path = tempnam(sys_get_temp_dir(), 'bar_').'.xlsx';
        (new Xlsx($spreadsheet))->save($path);

        return new UploadedFile($path, 'barriers.xlsx', null, null, true);
    }

    private function upload(FgdsCommunity $record, UploadedFile $file)
    {
        return $this->actingAs(User::factory()->create())
            ->post(route('admin.fgds-community.upload-barriers', $record->id), [
                'barriers_file' => $file,
            ]);
    }

    public function test_it_imports_barriers_with_their_serial_numbers_and_categories(): void
    {
        $record = $this->record();

        $this->upload($record, $this->xlsx([
            ['Sr. No', 'Identified Barriers', 'Category'],
            [1, 'Parents fear side effects', 'Fear of Side Effects and Vaccine Safety Concerns'],
            [2, 'The site is too far', 'Access Issues'],
        ]))->assertRedirect();

        $barriers = FgdsCommunityBarrier::with('category')->orderBy('serial_number')->get();

        $this->assertCount(2, $barriers);
        $this->assertSame('Parents fear side effects', $barriers[0]->barrier_text);
        $this->assertSame(1, $barriers[0]->serial_number);
        $this->assertSame('Fear of Side Effects and Vaccine Safety Concerns', $barriers[0]->category->name);
        $this->assertSame('Access Issues', $barriers[1]->category->name);
    }

    public function test_unknown_and_legacy_categories_still_land_inside_the_canonical_set(): void
    {
        $record = $this->record();
        $categoriesBefore = BarrierCategory::count();

        $this->upload($record, $this->xlsx([
            ['Sr. No', 'Identified Barriers', 'Category'],
            [1, 'Barrier one', 'Service Availability'],          // legacy label
            [2, 'Barrier two', 'Completely Invented Category'],  // unknown
            [3, 'Barrier three', ''],                            // blank
        ]))->assertRedirect();

        $barriers = FgdsCommunityBarrier::with('category')->get();

        $this->assertCount(3, $barriers);
        foreach ($barriers as $barrier) {
            $this->assertContains($barrier->category->name, BarrierCategory::CANONICAL);
        }

        $this->assertSame(
            $categoriesBefore,
            BarrierCategory::count(),
            'The import created a new barrier category.'
        );
    }

    public function test_rows_without_barrier_text_are_skipped(): void
    {
        $record = $this->record();

        $this->upload($record, $this->xlsx([
            ['Sr. No', 'Identified Barriers', 'Category'],
            [1, 'Real barrier', 'Access Issues'],
            [2, '', 'Access Issues'],
            [3, '   ', 'Access Issues'],
        ]))->assertRedirect();

        $this->assertSame(1, FgdsCommunityBarrier::count());
    }

    public function test_a_second_upload_replaces_the_previous_barriers(): void
    {
        $record = $this->record();

        $this->upload($record, $this->xlsx([
            ['Sr. No', 'Identified Barriers', 'Category'],
            [1, 'Old one', 'Access Issues'],
            [2, 'Old two', 'Access Issues'],
        ]))->assertRedirect();

        $this->assertSame(2, FgdsCommunityBarrier::count());

        $this->upload($record, $this->xlsx([
            ['Sr. No', 'Identified Barriers', 'Category'],
            [1, 'New one', 'Access Issues'],
        ]))->assertRedirect();

        $this->assertSame(1, FgdsCommunityBarrier::count());
        $this->assertSame('New one', FgdsCommunityBarrier::sole()->barrier_text);
    }

    public function test_uploading_barriers_for_one_record_leaves_another_records_barriers_alone(): void
    {
        $first = $this->record();
        $second = $this->record();

        $this->upload($first, $this->xlsx([
            ['Sr. No', 'Identified Barriers', 'Category'],
            [1, 'First record barrier', 'Access Issues'],
        ]))->assertRedirect();

        $this->upload($second, $this->xlsx([
            ['Sr. No', 'Identified Barriers', 'Category'],
            [1, 'Second record barrier', 'Access Issues'],
        ]))->assertRedirect();

        $this->assertSame(1, FgdsCommunityBarrier::where('fgds_community_id', $first->id)->count());
        $this->assertSame(1, FgdsCommunityBarrier::where('fgds_community_id', $second->id)->count());
    }

    public function test_the_barriers_by_category_drilldown_counts_only_matching_barriers(): void
    {
        $record = $this->record();

        $this->upload($record, $this->xlsx([
            ['Sr. No', 'Identified Barriers', 'Category'],
            [1, 'Too far to walk', 'Access Issues'],
            [2, 'No transport', 'Access Issues'],
            [3, 'Afraid of side effects', 'Fear of Side Effects and Vaccine Safety Concerns'],
        ]))->assertRedirect();

        $accessIssues = BarrierCategory::where('name', 'Access Issues')->sole();

        $response = $this->actingAs(User::factory()->create())
            ->getJson(route('admin.fgds-community.barriers-by-category', $accessIssues));

        $response->assertOk();
        $response->assertJsonPath('category', 'Access Issues');
        $response->assertJsonPath('count', 2);
    }

    public function test_the_drilldown_reports_the_consolidated_uc_name(): void
    {
        $record = $this->record(); // uc = 'Gujro Zone C'

        $this->upload($record, $this->xlsx([
            ['Sr. No', 'Identified Barriers', 'Category'],
            [1, 'Too far to walk', 'Access Issues'],
        ]))->assertRedirect();

        $accessIssues = BarrierCategory::where('name', 'Access Issues')->sole();

        $this->actingAs(User::factory()->create())
            ->getJson(route('admin.fgds-community.barriers-by-category', $accessIssues))
            ->assertOk()
            ->assertJsonPath('records.0.uc', 'Gujro');
    }

    public function test_it_rejects_a_non_spreadsheet_upload(): void
    {
        $record = $this->record();

        $this->actingAs(User::factory()->create())
            ->post(route('admin.fgds-community.upload-barriers', $record->id), [
                'barriers_file' => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf'),
            ])
            ->assertSessionHasErrors('barriers_file');

        $this->assertSame(0, FgdsCommunityBarrier::count());
    }
}
