<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MakeAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_verified_admin_account(): void
    {
        $this->artisan('gulraiz:make-admin', ['email' => 'Hello@Gulraiz.ca'])
            ->expectsQuestion('Name', 'Mohammed Gulraiz')
            ->expectsQuestion('Password', 'a-strong-password')
            ->assertSuccessful();

        $admin = User::sole();
        $this->assertSame('hello@gulraiz.ca', $admin->email);
        $this->assertTrue($admin->is_admin);
        $this->assertTrue($admin->hasVerifiedEmail());
    }

    public function test_it_promotes_an_existing_account(): void
    {
        $user = User::factory()->create(['email' => 'hello@gulraiz.ca']);

        $this->artisan('gulraiz:make-admin', ['email' => 'hello@gulraiz.ca'])->assertSuccessful();

        $this->assertTrue($user->fresh()->is_admin);
    }

    public function test_it_rejects_a_short_password(): void
    {
        $this->artisan('gulraiz:make-admin', ['email' => 'hello@gulraiz.ca'])
            ->expectsQuestion('Name', 'Mohammed Gulraiz')
            ->expectsQuestion('Password', 'short')
            ->assertFailed();

        $this->assertDatabaseCount('users', 0);
    }
}
