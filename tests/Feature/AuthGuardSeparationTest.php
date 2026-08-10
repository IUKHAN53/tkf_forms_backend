<?php

namespace Tests\Feature;

use App\Models\CommunityMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * `sanctum` (staff/field workers, App\Models\User) and `community` (CLM Tracker
 * members, App\Models\CommunityMember) are two sanctum guards over one token
 * table. A token issued for one population must not open the other's endpoints.
 */
class AuthGuardSeparationTest extends TestCase
{
    use RefreshDatabase;

    private function communityMember(array $overrides = []): CommunityMember
    {
        return CommunityMember::create(array_merge([
            'name' => 'Community Member',
            'phone' => '03001234567',
            'password' => 'secret-password',
            'district' => 'Karachi',
            'uc' => 'Gujro Zone C',
            'fix_site' => 'BHU Gujro',
            'is_active' => true,
        ], $overrides));
    }

    public function test_a_community_member_can_log_in_and_receive_a_token(): void
    {
        $this->communityMember();

        $response = $this->postJson('/api/v1/clm/login', [
            'phone' => '03001234567',
            'password' => 'secret-password',
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['token', 'user' => ['id', 'name', 'phone']]);
        $response->assertJsonMissingPath('user.password');
    }

    public function test_login_is_rejected_for_a_wrong_password(): void
    {
        $this->communityMember();

        $this->postJson('/api/v1/clm/login', [
            'phone' => '03001234567',
            'password' => 'wrong',
        ])->assertUnauthorized();
    }

    public function test_a_deactivated_member_cannot_log_in(): void
    {
        $this->communityMember(['is_active' => false]);

        $this->postJson('/api/v1/clm/login', [
            'phone' => '03001234567',
            'password' => 'secret-password',
        ])->assertForbidden();
    }

    public function test_a_staff_token_cannot_reach_community_tracker_endpoints(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/vaccination-records')
            ->assertUnauthorized();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/clm/me')
            ->assertUnauthorized();
    }

    public function test_a_community_token_cannot_reach_staff_core_form_endpoints(): void
    {
        $member = $this->communityMember();

        $this->actingAs($member, 'community')
            ->getJson('/api/v1/fgds-community')
            ->assertUnauthorized();

        $this->actingAs($member, 'community')
            ->postJson('/api/v1/bridging-the-gap', [])
            ->assertUnauthorized();

        $this->actingAs($member, 'community')
            ->getJson('/api/v1/dashboard/stats')
            ->assertUnauthorized();
    }

    public function test_a_community_token_reaches_its_own_endpoints(): void
    {
        $member = $this->communityMember();

        $this->actingAs($member, 'community')
            ->getJson('/api/v1/clm/me')
            ->assertOk()
            ->assertJsonPath('phone', '03001234567');

        $this->actingAs($member, 'community')
            ->getJson('/api/v1/vaccination-records')
            ->assertOk();
    }

    public function test_public_endpoints_stay_open_to_both(): void
    {
        // Dropdown data the mobile app needs before anyone has logged in.
        $this->getJson('/api/v1/outreach-sites/districts')->assertOk();
        $this->getJson('/api/v1/outreach-sites/union-councils')->assertOk();
    }
}
