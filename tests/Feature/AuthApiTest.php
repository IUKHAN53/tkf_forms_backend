<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Staff / field-worker API login. Note this authenticates on `phone`, not email
 * — the mobile app has no email field.
 */
class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_phone_and_receive_token(): void
    {
        $user = User::factory()->create([
            'phone' => '03001234567',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'phone' => $user->phone,
            'password' => 'password',
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['token', 'user' => ['id', 'phone']]);
        $this->assertNotEmpty($response->json('token'));
    }

    public function test_login_requires_a_phone_number(): void
    {
        $this->postJson('/api/v1/login', ['password' => 'password'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('phone');
    }

    public function test_login_is_rejected_for_a_wrong_password(): void
    {
        $user = User::factory()->create([
            'phone' => '03001234567',
            'password' => bcrypt('password'),
        ]);

        $this->postJson('/api/v1/login', [
            'phone' => $user->phone,
            'password' => 'not-the-password',
        ])->assertUnauthorized();
    }

    public function test_login_is_rejected_for_an_unknown_phone(): void
    {
        $this->postJson('/api/v1/login', [
            'phone' => '03009999999',
            'password' => 'password',
        ])->assertUnauthorized();
    }

    public function test_the_issued_token_authenticates_subsequent_requests(): void
    {
        $user = User::factory()->create([
            'phone' => '03001234567',
            'password' => bcrypt('password'),
        ]);

        $token = $this->postJson('/api/v1/login', [
            'phone' => $user->phone,
            'password' => 'password',
        ])->json('token');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/fgds-community')
            ->assertOk();
    }
}
