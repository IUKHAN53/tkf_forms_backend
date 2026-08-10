<?php

namespace Tests\Feature;

use App\Models\BridgingTheGap;
use App\Models\BridgingTheGapActionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

/**
 * Action plans arrive as spreadsheets staff assemble by hand, so the column
 * layout varies: a leading "Sr. No", a missing "Sub Cause" or "Root Cause",
 * reordered columns. Mapping by header name rather than position is what makes
 * those all import correctly — regression cover for the bug fixed in 3b0a55c.
 *
 * The canonical layout is:
 *   Problem | Sub Cause | Root Cause | Solution | Action Needed | Responsible | Timeline
 */
class ActionPlanImportTest extends TestCase
{
    use RefreshDatabase;

    /** The canonical header row, as emitted by the downloadable sample. */
    private const CANONICAL_HEADER = [
        'Problem', 'Sub Cause', 'Root Cause', 'Solution', 'Action Needed', 'Responsible', 'Timeline',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    private function record(): BridgingTheGap
    {
        return BridgingTheGap::factory()->create();
    }

    /**
     * Build a real .xlsx on disk — the controller runs it through PhpSpreadsheet,
     * so a fake file would not exercise the parsing path.
     */
    private function xlsx(array $rows): UploadedFile
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->fromArray($rows, null, 'A1');

        $path = tempnam(sys_get_temp_dir(), 'ap_').'.xlsx';
        (new Xlsx($spreadsheet))->save($path);

        return new UploadedFile($path, 'action-plan.xlsx', null, null, true);
    }

    private function upload(BridgingTheGap $record, UploadedFile $file)
    {
        return $this->actingAs(User::factory()->create())
            ->post(route('admin.bridging-the-gap.upload-action-plan', $record->id), [
                'action_plan_file' => $file,
            ]);
    }

    public function test_it_imports_the_canonical_layout(): void
    {
        $record = $this->record();

        $this->upload($record, $this->xlsx([
            self::CANONICAL_HEADER,
            ['Low turnout', 'Few outreach visits', 'No awareness', 'Awareness drive', 'Hold sessions', 'Team A', '2 weeks'],
        ]))->assertRedirect();

        $plan = BridgingTheGapActionPlan::sole();

        $this->assertSame('Low turnout', $plan->problem);
        $this->assertSame('Few outreach visits', $plan->sub_cause);
        $this->assertSame('No awareness', $plan->root_cause);
        $this->assertSame('Awareness drive', $plan->solution);
        $this->assertSame('Hold sessions', $plan->action_needed);
        $this->assertSame('Team A', $plan->who_is_responsible);
        $this->assertSame('2 weeks', $plan->timeline);
        $this->assertSame(1, $plan->serial_number);
    }

    /**
     * "Sub Cause" and "Root Cause" both contain "cause" once punctuation is
     * stripped, so this pins down that neither swallows the other's column.
     */
    public function test_sub_cause_and_root_cause_do_not_capture_each_other(): void
    {
        $record = $this->record();

        $this->upload($record, $this->xlsx([
            self::CANONICAL_HEADER,
            ['P', 'THE SUB CAUSE', 'THE ROOT CAUSE', 's', 'a', 'r', 't'],
        ]))->assertRedirect();

        $plan = BridgingTheGapActionPlan::sole();

        $this->assertSame('THE SUB CAUSE', $plan->sub_cause);
        $this->assertSame('THE ROOT CAUSE', $plan->root_cause);
    }

    public function test_a_bare_cause_column_is_treated_as_root_cause(): void
    {
        $record = $this->record();

        $this->upload($record, $this->xlsx([
            ['Problem', 'Cause', 'Solution', 'Action Needed', 'Responsible', 'Timeline'],
            ['Low turnout', 'No awareness', 'Awareness drive', 'Hold sessions', 'Team A', '2 weeks'],
        ]))->assertRedirect();

        $plan = BridgingTheGapActionPlan::sole();

        $this->assertSame('No awareness', $plan->root_cause);
        $this->assertNull($plan->sub_cause);
    }

    public function test_a_leading_serial_number_column_is_ignored(): void
    {
        $record = $this->record();

        $this->upload($record, $this->xlsx([
            array_merge(['Sr. No'], self::CANONICAL_HEADER),
            [1, 'Low turnout', 'Few outreach visits', 'No awareness', 'Awareness drive', 'Hold sessions', 'Team A', '2 weeks'],
        ]))->assertRedirect();

        $plan = BridgingTheGapActionPlan::sole();

        // Positional mapping would have put the serial number in `problem`.
        $this->assertSame('Low turnout', $plan->problem);
        $this->assertSame('Few outreach visits', $plan->sub_cause);
        $this->assertSame('No awareness', $plan->root_cause);
        $this->assertSame('2 weeks', $plan->timeline);
    }

    public function test_columns_may_appear_in_any_order(): void
    {
        $record = $this->record();

        $this->upload($record, $this->xlsx([
            ['Timeline', 'Responsible', 'Problem', 'Solution', 'Root Cause', 'Sub Cause', 'Action Needed'],
            ['2 weeks', 'Team A', 'Low turnout', 'Awareness drive', 'No awareness', 'Few outreach visits', 'Hold sessions'],
        ]))->assertRedirect();

        $plan = BridgingTheGapActionPlan::sole();

        $this->assertSame('Low turnout', $plan->problem);
        $this->assertSame('Few outreach visits', $plan->sub_cause);
        $this->assertSame('No awareness', $plan->root_cause);
        $this->assertSame('Team A', $plan->who_is_responsible);
        $this->assertSame('2 weeks', $plan->timeline);
    }

    /**
     * Files produced before Sub Cause existed must keep importing, with every
     * other column landing where it did before.
     */
    public function test_a_legacy_file_without_a_sub_cause_column_still_imports(): void
    {
        $record = $this->record();

        $this->upload($record, $this->xlsx([
            ['Problem', 'Root Cause', 'Solution', 'Action Needed', 'Responsible', 'Timeline'],
            ['Low turnout', 'No awareness', 'Awareness drive', 'Hold sessions', 'Team A', '2 weeks'],
        ]))->assertRedirect();

        $plan = BridgingTheGapActionPlan::sole();

        $this->assertSame('Low turnout', $plan->problem);
        $this->assertNull($plan->sub_cause);
        // Nothing may shift left into the absent column.
        $this->assertSame('No awareness', $plan->root_cause);
        $this->assertSame('Awareness drive', $plan->solution);
        $this->assertSame('Hold sessions', $plan->action_needed);
        $this->assertSame('Team A', $plan->who_is_responsible);
        $this->assertSame('2 weeks', $plan->timeline);
    }

    public function test_a_file_without_a_root_cause_column_still_imports(): void
    {
        $record = $this->record();

        $this->upload($record, $this->xlsx([
            ['Problem', 'Sub Cause', 'Solution', 'Action Needed', 'Responsible', 'Timeline'],
            ['Low turnout', 'Few outreach visits', 'Awareness drive', 'Hold sessions', 'Team A', '2 weeks'],
        ]))->assertRedirect();

        $plan = BridgingTheGapActionPlan::sole();

        $this->assertSame('Few outreach visits', $plan->sub_cause);
        $this->assertNull($plan->root_cause);
        $this->assertSame('Awareness drive', $plan->solution);
        $this->assertSame('2 weeks', $plan->timeline);
    }

    public function test_header_matching_tolerates_case_and_punctuation(): void
    {
        $record = $this->record();

        $this->upload($record, $this->xlsx([
            ['  problem  ', 'SUB-CAUSE', 'ROOT-CAUSE', 'Solution(s)', 'Actions Needed', 'responsible person', 'Dead Line'],
            ['Low turnout', 'Few outreach visits', 'No awareness', 'Awareness drive', 'Hold sessions', 'Team A', '2 weeks'],
        ]))->assertRedirect();

        $plan = BridgingTheGapActionPlan::sole();

        $this->assertSame('Few outreach visits', $plan->sub_cause);
        $this->assertSame('No awareness', $plan->root_cause);
        $this->assertSame('Team A', $plan->who_is_responsible);
        $this->assertSame('2 weeks', $plan->timeline);
    }

    public function test_an_unrecognised_header_falls_back_to_the_positional_layout(): void
    {
        $record = $this->record();

        $this->upload($record, $this->xlsx([
            ['A', 'B', 'C', 'D', 'E', 'F', 'G'],
            ['Low turnout', 'Few outreach visits', 'No awareness', 'Awareness drive', 'Hold sessions', 'Team A', '2 weeks'],
        ]))->assertRedirect();

        $plan = BridgingTheGapActionPlan::sole();

        $this->assertSame('Low turnout', $plan->problem);
        $this->assertSame('Few outreach visits', $plan->sub_cause);
        $this->assertSame('No awareness', $plan->root_cause);
        $this->assertSame('2 weeks', $plan->timeline);
    }

    public function test_rows_without_a_problem_are_skipped_and_numbering_stays_contiguous(): void
    {
        $record = $this->record();

        $this->upload($record, $this->xlsx([
            self::CANONICAL_HEADER,
            ['First', 'sc', 'a', 'b', 'c', 'd', 'e'],
            ['', '', '', '', '', '', ''],
            ['Second', 'sc', 'a', 'b', 'c', 'd', 'e'],
        ]))->assertRedirect();

        $plans = BridgingTheGapActionPlan::orderBy('serial_number')->get();

        $this->assertCount(2, $plans);
        $this->assertSame(['First', 'Second'], $plans->pluck('problem')->all());
        $this->assertSame([1, 2], $plans->pluck('serial_number')->all());
    }

    public function test_a_second_upload_replaces_the_previous_action_plans(): void
    {
        $record = $this->record();

        $this->upload($record, $this->xlsx([
            self::CANONICAL_HEADER,
            ['Old one', 'sc', 'a', 'b', 'c', 'd', 'e'],
            ['Old two', 'sc', 'a', 'b', 'c', 'd', 'e'],
        ]))->assertRedirect();

        $this->assertSame(2, BridgingTheGapActionPlan::count());

        $this->upload($record, $this->xlsx([
            self::CANONICAL_HEADER,
            ['New one', 'sc', 'a', 'b', 'c', 'd', 'e'],
        ]))->assertRedirect();

        // Import replaces rather than merges.
        $this->assertSame(1, BridgingTheGapActionPlan::count());
        $this->assertSame('New one', BridgingTheGapActionPlan::sole()->problem);
    }

    public function test_uploading_records_the_stored_file_path_on_the_record(): void
    {
        $record = $this->record();

        $this->upload($record, $this->xlsx([
            self::CANONICAL_HEADER,
            ['Low turnout', 'sc', 'a', 'b', 'c', 'd', 'e'],
        ]))->assertRedirect();

        $record->refresh();

        $this->assertNotNull($record->action_plan_file);
        $this->assertStringStartsWith('action_plans/bridging_the_gap/', $record->action_plan_file);
        Storage::disk('public')->assertExists($record->action_plan_file);
    }

    public function test_it_rejects_a_non_spreadsheet_upload(): void
    {
        $record = $this->record();

        $this->actingAs(User::factory()->create())
            ->post(route('admin.bridging-the-gap.upload-action-plan', $record->id), [
                'action_plan_file' => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf'),
            ])
            ->assertSessionHasErrors('action_plan_file');

        $this->assertSame(0, BridgingTheGapActionPlan::count());
    }

    /**
     * The sample the user downloads must be importable as-is, and must carry the
     * canonical columns — otherwise the round trip silently drops a field.
     */
    public function test_the_downloadable_sample_matches_the_canonical_layout_and_reimports(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.bridging-the-gap.action-plan-sample'));
        $response->assertOk();

        $path = tempnam(sys_get_temp_dir(), 'sample_').'.xlsx';
        file_put_contents($path, $response->streamedContent());

        $rows = \PhpOffice\PhpSpreadsheet\IOFactory::load($path)->getActiveSheet()->toArray();

        $this->assertSame(self::CANONICAL_HEADER, array_slice($rows[0], 0, 7));

        // Feed the generated sample straight back into the importer.
        $record = $this->record();
        $this->upload($record, new UploadedFile($path, 'action_plan_sample_template.xlsx', null, null, true))
            ->assertRedirect();

        $plans = BridgingTheGapActionPlan::orderBy('serial_number')->get();

        $this->assertCount(3, $plans);
        foreach ($plans as $plan) {
            $this->assertNotEmpty($plan->problem);
            $this->assertNotEmpty($plan->sub_cause, 'The sample must demonstrate the Sub Cause column.');
            $this->assertNotEmpty($plan->root_cause);
            $this->assertNotEmpty($plan->timeline);
        }
    }
}
