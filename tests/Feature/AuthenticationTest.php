<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_can_register_and_is_asked_to_verify_their_email(): void
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'Jane Baker',
            'email' => 'jane@example.com',
            'password' => 'a-strong-password',
            'password_confirmation' => 'a-strong-password',
        ])->assertRedirect(route('portal.dashboard'));

        $user = User::sole();
        $this->assertAuthenticatedAs($user);
        $this->assertFalse($user->is_admin);
        Notification::assertSentTo($user, VerifyEmail::class);

        $this->get(route('portal.dashboard'))->assertRedirect(route('verification.notice'));
    }

    public function test_registration_cannot_grant_admin_access(): void
    {
        $this->post('/register', [
            'name' => 'Sneaky',
            'email' => 'sneaky@example.com',
            'password' => 'a-strong-password',
            'password_confirmation' => 'a-strong-password',
            'is_admin' => true,
        ]);

        $this->assertFalse(User::sole()->is_admin);
    }

    public function test_verification_link_verifies_the_email(): void
    {
        $user = User::factory()->unverified()->create();

        $url = URL::temporarySignedRoute('verification.verify', now()->addHour(), [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]);

        $this->actingAs($user)->get($url)->assertRedirect(route('portal.dashboard').'?verified=1');
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_client_logs_in_to_their_account(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('portal.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_logs_in_to_the_admin_panel(): void
    {
        $admin = User::factory()->admin()->create();

        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_login_fails_with_the_wrong_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_is_rate_limited(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 5) as $attempt) {
            $this->post('/login', ['email' => $user->email, 'password' => 'wrong']);
        }

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertTooManyRequests();
    }

    public function test_user_can_reset_their_password(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email])->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user): bool {
            $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'a-new-strong-password',
                'password_confirmation' => 'a-new-strong-password',
            ])->assertRedirect(route('login'));

            return true;
        });

        $this->post('/login', ['email' => $user->email, 'password' => 'a-new-strong-password'])->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_log_out(): void
    {
        $this->actingAs(User::factory()->create())->post('/logout')->assertRedirect('/');

        $this->assertGuest();
    }
}
