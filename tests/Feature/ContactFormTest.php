<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Notifications\ContactMessageReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_can_send_a_message_and_admin_is_notified(): void
    {
        Notification::fake();

        $this->post('/contact', [
            'name' => 'Jane Baker',
            'email' => 'jane@example.com',
            'message' => 'Do you build sites for bakeries?',
        ])->assertRedirect('/contact')->assertSessionHas('status');

        $message = ContactMessage::sole();
        $this->assertSame('Jane Baker', $message->name);
        $this->assertNull($message->read_at);

        Notification::assertSentOnDemand(
            ContactMessageReceived::class,
            fn (ContactMessageReceived $notification, array $channels, object $notifiable) => $notifiable->routes['mail'] === config('gulraiz.admin_email')
                && $notification->contactMessage->is($message),
        );
    }

    public function test_message_is_rejected_when_the_spam_trap_is_filled(): void
    {
        Notification::fake();

        $this->post('/contact', [
            'name' => 'Bot',
            'email' => 'bot@example.com',
            'message' => 'Buy now',
            'company_website' => 'https://spam.example',
        ])->assertSessionHasErrors('company_website');

        $this->assertDatabaseCount('contact_messages', 0);
        Notification::assertNothingSent();
    }

    public function test_name_email_and_message_are_required(): void
    {
        $this->post('/contact', [])->assertSessionHasErrors(['name', 'email', 'message']);
    }
}
