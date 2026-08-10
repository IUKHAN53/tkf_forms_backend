<?php

namespace Tests\Feature;

use App\Models\BridgingTheGap;
use App\Models\BridgingTheGapActionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Action plans can also be added and edited by hand in the manage modal, which
 * posts JSON. Those endpoints must carry the same column set as the importer.
 */
class ActionPlanCrudTest extends TestCase
{
    use RefreshDatabase;

    private function record(): BridgingTheGap
    {
        return BridgingTheGap::factory()->create();
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'problem' => 'Low turnout',
            'sub_cause' => 'Few outreach visits',
            'root_cause' => 'No awareness',
            'solution' => 'Awareness drive',
            'action_needed' => 'Hold sessions',
            'who_is_responsible' => 'Team A',
            'timeline' => '2 weeks',
        ], $overrides);
    }

    public function test_it_stores_an_action_plan_with_a_sub_cause(): void
    {
        $record = $this->record();

        $response = $this->actingAs(User::factory()->create())
            ->postJson(route('admin.bridging-the-gap.store-action-plan', $record->id), $this->payload());

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('action_plan.sub_cause', 'Few outreach visits');

        $plan = BridgingTheGapActionPlan::sole();
        $this->assertSame('Few outreach visits', $plan->sub_cause);
        $this->assertSame('No awareness', $plan->root_cause);
        $this->assertSame(1, $plan->serial_number);
    }

    public function test_sub_cause_is_optional(): void
    {
        $record = $this->record();

        $this->actingAs(User::factory()->create())
            ->postJson(route('admin.bridging-the-gap.store-action-plan', $record->id), $this->payload(['sub_cause' => null]))
            ->assertOk();

        $this->assertNull(BridgingTheGapActionPlan::sole()->sub_cause);
    }

    public function test_problem_is_still_required(): void
    {
        $record = $this->record();

        $this->actingAs(User::factory()->create())
            ->postJson(route('admin.bridging-the-gap.store-action-plan', $record->id), $this->payload(['problem' => '']))
            ->assertStatus(422)
            ->assertJsonValidationErrors('problem');

        $this->assertSame(0, BridgingTheGapActionPlan::count());
    }

    public function test_serial_numbers_continue_from_the_existing_maximum(): void
    {
        $record = $this->record();
        $user = User::factory()->create();

        foreach (['First', 'Second'] as $problem) {
            $this->actingAs($user)
                ->postJson(route('admin.bridging-the-gap.store-action-plan', $record->id), $this->payload(['problem' => $problem]))
                ->assertOk();
        }

        $this->assertSame([1, 2], BridgingTheGapActionPlan::orderBy('serial_number')->pluck('serial_number')->all());
    }

    public function test_it_updates_the_sub_cause(): void
    {
        $record = $this->record();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('admin.bridging-the-gap.store-action-plan', $record->id), $this->payload())
            ->assertOk();

        $plan = BridgingTheGapActionPlan::sole();

        $this->actingAs($user)
            ->putJson(route('admin.bridging-the-gap.update-action-plan', $plan->id), $this->payload([
                'sub_cause' => 'Revised sub cause',
            ]))
            ->assertOk()
            ->assertJsonPath('action_plan.sub_cause', 'Revised sub cause');

        $this->assertSame('Revised sub cause', $plan->fresh()->sub_cause);
        // The neighbouring column must not be disturbed.
        $this->assertSame('No awareness', $plan->fresh()->root_cause);
    }

    public function test_the_action_plans_json_endpoint_exposes_the_sub_cause(): void
    {
        $record = $this->record();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('admin.bridging-the-gap.store-action-plan', $record->id), $this->payload())
            ->assertOk();

        // This is what the manage-modal table renders from.
        $this->actingAs($user)
            ->getJson(route('admin.bridging-the-gap.action-plans', $record->id))
            ->assertOk()
            ->assertJsonPath('action_plans.0.sub_cause', 'Few outreach visits')
            ->assertJsonPath('action_plans.0.root_cause', 'No awareness');
    }
}
