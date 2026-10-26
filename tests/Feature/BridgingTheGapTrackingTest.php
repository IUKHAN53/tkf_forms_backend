<?php

namespace Tests\Feature;

use App\Models\BridgingTheGap;
use App\Models\BridgingTheGapActionPlan;
use App\Models\BridgingTheGapActionPlanPhoto;
use App\Models\BridgingTheGapTeamMember;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * IIT member management, the editable BTG attendance sheet, and action-plan
 * implementation tracking (status, remarks, activity pictures).
 */
class BridgingTheGapTrackingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    private function attendee(BridgingTheGap $session, string $name = 'Ali Raza', array $extra = []): Participant
    {
        return $session->addAttendee(array_merge(['name' => $name, 'gender' => 'Male'], $extra));
    }

    private function member(BridgingTheGap $session, Participant $p): BridgingTheGapTeamMember
    {
        return BridgingTheGapTeamMember::create([
            'bridging_the_gap_id' => $session->id,
            'participant_id' => $p->id,
            'source_type' => 'bridging_the_gap',
            'source_id' => $session->id,
        ]);
    }

    /* ------------------------------ IIT members ------------------------------ */

    public function test_iit_members_page_renders_with_and_without_members(): void
    {
        $this->get(route('admin.iit-members.index'))->assertOk()->assertSee('IIT Members');

        $session = BridgingTheGap::factory()->create();
        $this->member($session, $this->attendee($session, 'Zainab Bibi', ['gender' => 'Female', 'title_designation' => 'LHW']));

        $this->get(route('admin.iit-members.index'))
            ->assertOk()
            ->assertSee('Zainab Bibi')
            ->assertSee('LHW')
            ->assertSee($session->unique_id);
    }

    public function test_a_member_can_be_chosen_from_the_sessions_own_attendance(): void
    {
        $session = BridgingTheGap::factory()->create();
        $person = $this->attendee($session);

        $this->post(route('admin.iit-members.store'), [
            'bridging_the_gap_id' => $session->id,
            'participant_id' => $person->id,
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('bridging_the_gap_team_members', [
            'bridging_the_gap_id' => $session->id,
            'participant_id' => $person->id,
            'source_type' => 'bridging_the_gap',
        ]);
    }

    public function test_a_person_from_another_session_cannot_be_chosen(): void
    {
        $session = BridgingTheGap::factory()->create();
        $other = BridgingTheGap::factory()->create();
        $stranger = $this->attendee($other, 'Not Here');

        $this->post(route('admin.iit-members.store'), [
            'bridging_the_gap_id' => $session->id,
            'participant_id' => $stranger->id,
        ])->assertSessionHas('error');

        $this->assertSame(0, BridgingTheGapTeamMember::count());
    }

    public function test_registering_a_new_member_adds_them_to_the_attendance_sheet_too(): void
    {
        $session = BridgingTheGap::factory()->create();
        $this->attendee($session, 'Existing Person');

        $this->post(route('admin.iit-members.store'), [
            'bridging_the_gap_id' => $session->id,
            'name' => 'New Volunteer',
            'gender' => 'Female',
            'title_designation' => 'Teacher',
            'contact_no' => '0300-1234567',
        ])->assertSessionHas('success');

        $person = Participant::where('name', 'New Volunteer')->firstOrFail();
        $this->assertTrue($session->ownsParticipant($person));
        $this->assertSame(2, $person->sr_no);
        $this->assertDatabaseHas('bridging_the_gap_team_members', ['participant_id' => $person->id, 'bridging_the_gap_id' => $session->id]);
    }

    public function test_adding_the_same_member_twice_is_rejected(): void
    {
        $session = BridgingTheGap::factory()->create();
        $person = $this->attendee($session);
        $this->member($session, $person);

        $this->post(route('admin.iit-members.store'), [
            'bridging_the_gap_id' => $session->id,
            'participant_id' => $person->id,
        ])->assertSessionHas('error');

        $this->assertSame(1, BridgingTheGapTeamMember::count());
    }

    public function test_candidates_are_the_sessions_attendees_not_yet_on_the_team(): void
    {
        $session = BridgingTheGap::factory()->create();
        $already = $this->attendee($session, 'Already In');
        $this->member($session, $already);
        $free = $this->attendee($session, 'Free Person');
        $this->attendee(BridgingTheGap::factory()->create(), 'Other Session');

        $ids = collect($this->getJson(route('admin.iit-members.candidates', ['bridging_the_gap_id' => $session->id]))
            ->assertOk()->json('candidates'))->pluck('id')->all();

        $this->assertSame([$free->id], $ids);
    }

    public function test_member_details_can_be_edited_and_member_removed_without_losing_attendance(): void
    {
        $session = BridgingTheGap::factory()->create();
        $person = $this->attendee($session);
        $member = $this->member($session, $person);

        $this->put(route('admin.iit-members.update', $member), [
            'name' => 'Ali Raza Khan',
            'title_designation' => 'Imam',
            'contact_no' => '0311-0000000',
        ])->assertSessionHas('success');

        $this->assertSame('Imam', $person->fresh()->title_designation);
        $this->assertSame('Ali Raza Khan', $person->fresh()->name);

        $this->delete(route('admin.iit-members.destroy', $member))->assertSessionHas('success');

        $this->assertSame(0, BridgingTheGapTeamMember::count());
        $this->assertNotNull($person->fresh(), 'Removing from the team must keep the attendance record.');
    }

    public function test_iit_members_export_streams_csv(): void
    {
        $session = BridgingTheGap::factory()->create();
        $this->member($session, $this->attendee($session, 'Csv Person'));

        $response = $this->get(route('admin.iit-members.export'));
        $response->assertOk();
        $this->assertStringContainsString('Csv Person', $response->streamedContent());
    }

    /* --------------------------- Attendance sheet --------------------------- */

    public function test_attendance_can_be_added_edited_and_deleted_after_submission(): void
    {
        $session = BridgingTheGap::factory()->create();

        $this->post(route('admin.bridging-the-gap.participants.store', $session), [
            'name' => 'Late Arrival',
            'occupation' => 'Shopkeeper',
            'mark_iit' => '1',
        ])->assertSessionHas('success');

        $person = Participant::where('name', 'Late Arrival')->firstOrFail();
        $this->assertTrue($session->ownsParticipant($person));
        $this->assertDatabaseHas('bridging_the_gap_team_members', ['participant_id' => $person->id]);

        $this->put(route('admin.bridging-the-gap.participants.update', [$session, $person]), [
            'name' => 'Late Arrival (corrected)',
            'gender' => 'Male',
        ])->assertSessionHas('success');
        $this->assertSame('Late Arrival (corrected)', $person->fresh()->name);

        $this->delete(route('admin.bridging-the-gap.participants.destroy', [$session, $person]))->assertSessionHas('success');
        $this->assertNull($person->fresh());
        $this->assertSame(0, BridgingTheGapTeamMember::count(), 'Deleting an attendee must drop their IIT membership.');
    }

    public function test_attendance_of_another_session_cannot_be_edited_or_deleted(): void
    {
        $session = BridgingTheGap::factory()->create();
        $stranger = $this->attendee(BridgingTheGap::factory()->create(), 'Elsewhere');

        $this->put(route('admin.bridging-the-gap.participants.update', [$session, $stranger]), ['name' => 'Hacked'])
            ->assertSessionHas('error');
        $this->delete(route('admin.bridging-the-gap.participants.destroy', [$session, $stranger]))
            ->assertSessionHas('error');

        $this->assertSame('Elsewhere', $stranger->fresh()->name);
    }

    public function test_show_page_renders_attendance_iit_and_action_plan_sections(): void
    {
        $session = BridgingTheGap::factory()->create();
        $this->member($session, $this->attendee($session, 'Shown Person'));
        BridgingTheGapActionPlan::create([
            'bridging_the_gap_id' => $session->id,
            'problem' => 'Low coverage',
            'implementation_status' => 'in_progress',
            'serial_number' => 1,
        ]);

        $this->get(route('admin.bridging-the-gap.show', $session))
            ->assertOk()
            ->assertSee('Attendance Sheet')
            ->assertSee('IIT Team Members')
            ->assertSee('Shown Person')
            ->assertSee('Low coverage')
            ->assertSee('In Progress');

        $this->get(route('admin.bridging-the-gap.index'))->assertOk();
    }

    /* ------------------------- Action-plan tracking ------------------------- */

    public function test_new_action_plans_default_to_not_started(): void
    {
        $session = BridgingTheGap::factory()->create();

        $this->postJson(route('admin.bridging-the-gap.store-action-plan', $session->id), ['problem' => 'Refusals'])
            ->assertOk()
            ->assertJsonPath('action_plan.implementation_status', 'not_started')
            ->assertJsonPath('action_plan.implementation_status_label', 'Not Started');
    }

    public function test_status_can_be_updated_on_its_own_and_is_validated(): void
    {
        $plan = BridgingTheGapActionPlan::create([
            'bridging_the_gap_id' => BridgingTheGap::factory()->create()->id,
            'problem' => 'Cold chain',
            'serial_number' => 1,
        ]);

        $this->putJson(route('admin.bridging-the-gap.update-action-plan', $plan->id), ['implementation_status' => 'completed'])
            ->assertOk()
            ->assertJsonPath('action_plan.implementation_status', 'completed');

        $plan->refresh();
        $this->assertSame('Cold chain', $plan->problem, 'A status-only update must not clear other fields.');
        $this->assertNotNull($plan->status_updated_at);

        $this->putJson(route('admin.bridging-the-gap.update-action-plan', $plan->id), ['implementation_status' => 'abandoned'])
            ->assertStatus(422);
    }

    public function test_action_plan_can_be_revised_with_follow_up_remarks(): void
    {
        $plan = BridgingTheGapActionPlan::create([
            'bridging_the_gap_id' => BridgingTheGap::factory()->create()->id,
            'problem' => 'Hesitancy',
            'solution' => 'Old solution',
            'serial_number' => 1,
        ]);

        $this->putJson(route('admin.bridging-the-gap.update-action-plan', $plan->id), [
            'problem' => 'Hesitancy',
            'solution' => 'Revised after follow-up meeting',
            'implementation_status' => 'in_progress',
            'implementation_remarks' => 'Imam agreed to announce sessions',
        ])->assertOk();

        $plan->refresh();
        $this->assertSame('Revised after follow-up meeting', $plan->solution);
        $this->assertSame('Imam agreed to announce sessions', $plan->implementation_remarks);
    }

    public function test_activity_pictures_upload_serve_and_delete(): void
    {
        Storage::fake(BridgingTheGapActionPlanPhoto::DISK);

        $plan = BridgingTheGapActionPlan::create([
            'bridging_the_gap_id' => BridgingTheGap::factory()->create()->id,
            'problem' => 'Awareness',
            'serial_number' => 1,
        ]);

        $response = $this->postJson(route('admin.bridging-the-gap.action-plan-photos.store', $plan->id), [
            'photo' => UploadedFile::fake()->image('session.jpg', 800, 600),
        ])->assertOk();

        $photo = BridgingTheGapActionPlanPhoto::findOrFail($response->json('photo.id'));
        Storage::disk(BridgingTheGapActionPlanPhoto::DISK)->assertExists($photo->path);
        $this->assertStringNotContainsString('storage/', $photo->url, 'Pictures must not be served from a public path.');

        $this->getJson(route('admin.bridging-the-gap.action-plans', $plan->bridging_the_gap_id))
            ->assertJsonPath('action_plans.0.photos.0.id', $photo->id);

        $this->get($photo->url)->assertOk();

        $this->deleteJson(route('admin.bridging-the-gap.action-plan-photos.destroy', $photo->id))->assertOk();
        Storage::disk(BridgingTheGapActionPlanPhoto::DISK)->assertMissing($photo->path);
        $this->assertSame(0, BridgingTheGapActionPlanPhoto::count());
    }

    public function test_pictures_must_be_images_under_two_megabytes(): void
    {
        Storage::fake(BridgingTheGapActionPlanPhoto::DISK);
        $plan = BridgingTheGapActionPlan::create([
            'bridging_the_gap_id' => BridgingTheGap::factory()->create()->id,
            'problem' => 'x',
            'serial_number' => 1,
        ]);

        $this->postJson(route('admin.bridging-the-gap.action-plan-photos.store', $plan->id), [
            'photo' => UploadedFile::fake()->create('report.pdf', 100, 'application/pdf'),
        ])->assertStatus(422);

        $this->postJson(route('admin.bridging-the-gap.action-plan-photos.store', $plan->id), [
            'photo' => UploadedFile::fake()->image('huge.jpg')->size(3000),
        ])->assertStatus(422);
    }

    public function test_deleting_action_plans_also_deletes_their_picture_files(): void
    {
        Storage::fake(BridgingTheGapActionPlanPhoto::DISK);
        $session = BridgingTheGap::factory()->create();
        $plan = BridgingTheGapActionPlan::create(['bridging_the_gap_id' => $session->id, 'problem' => 'p', 'serial_number' => 1]);

        $path = $this->postJson(route('admin.bridging-the-gap.action-plan-photos.store', $plan->id), [
            'photo' => UploadedFile::fake()->image('a.jpg'),
        ])->json('photo.id');
        $stored = BridgingTheGapActionPlanPhoto::findOrFail($path)->path;

        $this->deleteJson(route('admin.bridging-the-gap.delete-all-action-plans', $session->id))->assertOk();

        Storage::disk(BridgingTheGapActionPlanPhoto::DISK)->assertMissing($stored);
        $this->assertSame(0, BridgingTheGapActionPlanPhoto::count());
        $this->assertSame(0, BridgingTheGapActionPlan::count());
    }

    /* ---------------------------- UC dashboard ---------------------------- */

    public function test_uc_dashboard_lists_iit_members_and_plan_progress(): void
    {
        $session = BridgingTheGap::factory()->create(['uc' => 'Gujro Zone C']);
        $this->member($session, $this->attendee($session, 'Uc Member', ['title_designation' => 'Councillor']));
        BridgingTheGapActionPlan::create(['bridging_the_gap_id' => $session->id, 'problem' => 'a', 'serial_number' => 1, 'implementation_status' => 'completed']);
        BridgingTheGapActionPlan::create(['bridging_the_gap_id' => $session->id, 'problem' => 'b', 'serial_number' => 2]);

        $iit = $this->getJson(route('admin.uc.data', ['slug' => 'gujro', 'tab' => 'iit_members']))->assertOk();
        $iit->assertJsonPath('data.stats.total_members', 1)
            ->assertJsonPath('data.records.0.name', 'Uc Member')
            ->assertJsonPath('data.records.0.role', 'Councillor')
            ->assertJsonPath('data.records.0.id', $session->id);

        $btg = $this->getJson(route('admin.uc.data', ['slug' => 'gujro', 'tab' => 'bridging_the_gap']))->assertOk();
        $btg->assertJsonPath('data.stats.action_plans_completed', 1)
            ->assertJsonPath('data.stats.action_plans_not_started', 1)
            ->assertJsonPath('data.records.0.action_plan_progress', '1/2 completed');

        $this->get(route('admin.uc.show', 'gujro'))->assertOk()->assertSee('IIT Members');
    }
}
