<?php

namespace Tests\Feature;

use App\Models\FgdsCommunity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Mobile submits carry a client-generated unique_id and the app retries when a
 * response is lost. On 27 Oct 2026 a submit saved fine, the app did not see the
 * response, and its three retries each hit the unique index and returned 500 —
 * so the field worker saw "Server Error" for a record that was already stored.
 */
class SubmitRetryTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'unique_id' => 'FC-RETRY001',
            'date' => '2026-10-27',
            'venue' => 'Molla Hussain Goth',
            'uc' => 'Songal-5',
            'district' => 'Karachi',
            'fix_site' => 'BHU Gujro',
            'outreach' => 'Outreach A',
            'community' => ['Mohalla One'],
            'participants_males' => 1,
            'participants_females' => 1,
            'facilitator_tkf' => 'Facilitator Name',
            'participants' => [
                ['name' => 'Ali', 'gender' => 'Male'],
                ['name' => 'Sana', 'gender' => 'Female'],
            ],
        ], $overrides);
    }

    public function test_resubmitting_the_same_unique_id_returns_the_stored_record(): void
    {
        $user = User::factory()->create();

        $first = $this->actingAs($user, 'sanctum')->postJson('/api/v1/fgds-community', $this->payload());
        $first->assertCreated();

        // The app never saw that response, so it sends the whole thing again.
        $retry = $this->actingAs($user, 'sanctum')->postJson('/api/v1/fgds-community', $this->payload());

        $retry->assertCreated();
        $retry->assertJsonPath('data.unique_id', 'FC-RETRY001');
        $this->assertSame($first->json('data.id'), $retry->json('data.id'));
    }

    public function test_a_retry_does_not_duplicate_the_record_or_its_participants(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 3) as $attempt) {
            $this->actingAs($user, 'sanctum')
                ->postJson('/api/v1/fgds-community', $this->payload())
                ->assertCreated();
        }

        $this->assertSame(1, FgdsCommunity::count());
        $this->assertCount(2, FgdsCommunity::sole()->participants);
    }

    public function test_the_retry_response_still_carries_the_participants(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/fgds-community', $this->payload())->assertCreated();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/fgds-community', $this->payload())
            ->assertCreated()
            ->assertJsonCount(2, 'data.participants');
    }

    public function test_a_submit_without_a_unique_id_still_creates_a_new_record(): void
    {
        $user = User::factory()->create();
        $payload = $this->payload();
        unset($payload['unique_id']);

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/fgds-community', $payload)->assertCreated();
        $this->actingAs($user, 'sanctum')->postJson('/api/v1/fgds-community', $payload)->assertCreated();

        // No id to match on, so these are two genuine submissions.
        $this->assertSame(2, FgdsCommunity::count());
    }

    public function test_a_different_unique_id_is_a_different_record(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/fgds-community', $this->payload())->assertCreated();
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/fgds-community', $this->payload(['unique_id' => 'FC-RETRY002']))
            ->assertCreated();

        $this->assertSame(2, FgdsCommunity::count());
    }
}
