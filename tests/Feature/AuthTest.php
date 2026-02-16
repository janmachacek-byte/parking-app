<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receive_token(): void
    {
        $res = $this->postJson('/api/auth/register', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $res->assertCreated()
            ->assertJsonStructure([
                'token',
                'user' => ['id', 'email'],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_user_can_login_and_receive_token(): void
    {
        $this->postJson('/api/auth/register', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ])->assertCreated();

        $res = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $res->assertOk()
            ->assertJsonStructure([
                'token',
                'user' => ['id', 'email'],
            ]);
    }

    public function test_me_requires_auth(): void
    {
        $this->getJson('/api/auth/me')
            ->assertUnauthorized();
    }
}