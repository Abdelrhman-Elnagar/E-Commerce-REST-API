<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Abdelrahman Elnagar',
            'email' => 'abdelrahman@mail.com',
            'password' => 'StrongPass123',
            'password_confirmation' => 'StrongPass123',
        ]);

        $response->assertCreated();

        $response->assertJson([
            'success' => true,
            'message' => 'Account created successfully.',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'abdelrahman@mail.com',
            'role' => 'customer',
            'status' => 'active',
        ]);
    }


    public function test_user_cannot_register_with_duplicate_email(): void
    {
        User::factory()->create([
            'email' => 'abdelrahman@mail.com',
        ]);

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Another User',
            'email' => 'abdelrahman@mail.com',
            'password' => 'StrongPass123',
            'password_confirmation' => 'StrongPass123',
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors([
            'email',
        ]);
    }


    public function test_registration_requires_password_confirmation(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Abdelrahman',
            'email' => 'new@example.com',
            'password' => 'StrongPass123',
            'password_confirmation' => 'DifferentPass123',
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors([
            'password',
        ]);
    }


    public function test_user_cannot_register_as_admin(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Attacker',
            'email' => 'attacker@example.com',
            'password' => 'StrongPass123',
            'password_confirmation' => 'StrongPass123',
            'role' => 'admin',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('users', [
            'email' => 'attacker@example.com',
            'role' => 'customer',
        ]);
    }


    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'abdelrahman@mail.com',
            'password' => 'StrongPass123',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'abdelrahman@mail.com',
            'password' => 'StrongPass123',
        ]);

        $response->assertOk();

        $response->assertJson([
            'success' => true,
            'message' => 'Login successful.',
        ]);

        $response->assertJsonPath(
            'data.user.id',
            $user->id
        );

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'user',
                'token',
            ],
        ]);
    }


    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'abdelrahman@mail.com',
            'password' => 'StrongPass123',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'abdelrahman@mail.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertUnauthorized();

        $response->assertJson([
            'success' => false,
            'message' => 'Invalid credentials.',
        ]);
    }


    public function test_authenticated_user_can_get_current_user(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this
            ->withToken($token)
            ->getJson('/api/v1/auth/me');

        $response->assertOk();

        $response->assertJsonPath(
            'data.user.id',
            $user->id
        );
    }

    
    public function test_guest_cannot_get_current_user(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertUnauthorized();
    }


    public function test_user_can_logout_and_token_is_revoked(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $logoutResponse = $this
            ->withToken($token)
            ->postJson('/api/v1/auth/logout');

        $logoutResponse->assertOk();

        $meResponse = $this
            ->withToken($token)
            ->getJson('/api/v1/auth/me');

        $meResponse->assertUnauthorized();
    }
}
