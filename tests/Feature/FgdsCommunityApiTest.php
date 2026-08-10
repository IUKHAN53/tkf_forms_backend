<?php

namespace Tests\Feature;

use App\Models\FgdsCommunity;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The mobile app's submit endpoint for FGDs-Community. This is the shape every
 * other core-form submit endpoint follows, including the cross-check that the
 * male/female counts a field worker typed match the attendance rows they filled.
 */
class FgdsCommunityApiTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'date' => '2026-07-01',
            'venue' => 'Community Hall',
            'uc' => 'Gujro Zone C',
            'district' => 'Karachi',
            'fix_site' => 'BHU Gujro',
            'outreach' => 'Outreach A',
            'community' => ['Mohalla One', 'Mohalla Two'],
            'participants_males' => 2,
            'participants_females' => 1,
            'facilitator_tkf' => 'Facilitator Name',
            'latitude' => 24.9056,
            'longitude' => 67.0822,
            'participants' => [
                ['name' => 'Ali',   'gender' => 'Male',   'contact_no' => '03001234567', 'cnic' => '42101-1234567-1'],
                ['name' => 'Bilal', 'gender' => 'Male'],
                ['name' => 'Sana',  'gender' => 'Female'],
            ],
        ], $overrides);
    }

    public function test_it_rejects_an_unauthenticated_submission(): void
    {
        $this->postJson('/api/v1/fgds-community', $this->payload())
            ->assertUnauthorized();
    }

    public function test_an_authenticated_field_worker_can_submit(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/fgds-community', $this->payload());

        $response->assertCreated();
        $response->assertJsonPath('message', 'FGDs-Community record created successfully');

        $this->assertDatabaseHas('fgds_community', [
            'user_id' => $user->id,
            'venue' => 'Community Hall',
            'uc' => 'Gujro Zone C',
        ]);
    }

    public function test_it_stamps_a_unique_id_with_the_form_prefix(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/fgds-community', $this->payload())
            ->assertCreated();

        $record = FgdsCommunity::sole();

        $this->assertMatchesRegularExpression('/^FC-[A-Z0-9]{8}$/', $record->unique_id);
    }

    public function test_it_stores_each_participant_as_a_polymorphic_row(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/fgds-community', $this->payload())
            ->assertCreated();

        $record = FgdsCommunity::sole();

        $this->assertCount(3, $record->participants);
        $this->assertDatabaseHas('participants', [
            'participantable_type' => FgdsCommunity::class,
            'participantable_id' => $record->id,
            'name' => 'Ali',
            'gender' => 'Male',
            'sr_no' => 1,
        ]);

        // Reporting counts come from these rows, not the typed-in integers.
        $this->assertSame(2, $record->participants()->where('gender', 'Male')->count());
        $this->assertSame(1, $record->participants()->where('gender', 'Female')->count());
    }

    public function test_it_rejects_a_male_count_that_disagrees_with_the_attendance_rows(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/fgds-community', $this->payload(['participants_males' => 5]));

        $response->assertStatus(422);
        $response->assertJsonPath('errors.participants_males.0', fn ($m) => str_contains($m, '(5)') && str_contains($m, '(2)'));

        $this->assertDatabaseCount('fgds_community', 0);
        $this->assertDatabaseCount('participants', 0);
    }

    public function test_it_rejects_a_female_count_that_disagrees_with_the_attendance_rows(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/fgds-community', $this->payload(['participants_females' => 0]))
            ->assertStatus(422)
            ->assertJsonPath('errors.participants_females.0', fn ($m) => str_contains($m, '(0)') && str_contains($m, '(1)'));
    }

    public function test_a_rejected_submission_leaves_nothing_behind(): void
    {
        $user = User::factory()->create();

        // A participant with an invalid phone fails validation after the shape
        // of the request has already been accepted; nothing may be persisted.
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/fgds-community', $this->payload([
                'participants' => [
                    ['name' => 'Ali', 'gender' => 'Male', 'contact_no' => '123'],
                ],
                'participants_males' => 1,
                'participants_females' => 0,
            ]))
            ->assertStatus(422);

        $this->assertDatabaseCount('fgds_community', 0);
        $this->assertDatabaseCount('participants', 0);
    }

    public function test_it_validates_pakistani_phone_and_cnic_formats(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/fgds-community', $this->payload([
                'participants' => [['name' => 'Ali', 'gender' => 'Male', 'cnic' => '4210-123-1']],
                'participants_males' => 1,
                'participants_females' => 0,
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('participants.0.cnic');
    }

    public function test_it_requires_at_least_one_participant(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/fgds-community', $this->payload([
                'participants' => [],
                'participants_males' => 0,
                'participants_females' => 0,
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('participants');
    }

    public function test_the_index_only_returns_the_callers_own_submissions(): void
    {
        $mine = User::factory()->create();
        $theirs = User::factory()->create();

        $this->actingAs($mine, 'sanctum')->postJson('/api/v1/fgds-community', $this->payload())->assertCreated();
        $this->actingAs($theirs, 'sanctum')->postJson('/api/v1/fgds-community', $this->payload(['venue' => 'Other Hall']))->assertCreated();

        $response = $this->actingAs($mine, 'sanctum')->getJson('/api/v1/fgds-community');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.venue', 'Community Hall');
    }

    public function test_deleting_a_record_through_the_admin_panel_cleans_up_its_participants(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/fgds-community', $this->payload())
            ->assertCreated();

        $record = FgdsCommunity::sole();
        $this->assertSame(3, Participant::count());

        // There are no cascade constraints — the controller does this by hand.
        $this->actingAs($user)
            ->delete(route('admin.fgds-community.destroy', $record))
            ->assertRedirect();

        $this->assertDatabaseCount('fgds_community', 0);
        $this->assertSame(0, Participant::count(), 'Orphaned participant rows were left behind.');
    }
}
