<?php

namespace Tests\Feature;

use App\Enums\EditRequestStatus;
use App\Enums\OrderStatus;
use App\Models\ContactMessage;
use App\Models\EditRequest;
use App\Models\IntakeSubmission;
use App\Models\Order;
use App\Models\Setting;
use App\Models\SiteTemplate;
use App\Models\User;
use App\Notifications\OrderStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string}>
     */
    public static function adminPages(): array
    {
        return [
            'dashboard' => ['admin.dashboard'],
            'orders' => ['admin.orders.index'],
            'edit requests' => ['admin.edit-requests.index'],
            'messages' => ['admin.contact-messages.index'],
            'templates' => ['admin.site-templates.index'],
            'new template' => ['admin.site-templates.create'],
            'portfolio' => ['admin.portfolio-items.index'],
            'new portfolio item' => ['admin.portfolio-items.create'],
            'about' => ['admin.about.edit'],
        ];
    }

    #[DataProvider('adminPages')]
    public function test_clients_cannot_open_admin_pages(string $routeName): void
    {
        $this->actingAs(User::factory()->create())->get(route($routeName))->assertForbidden();
    }

    #[DataProvider('adminPages')]
    public function test_admin_can_open_admin_pages(string $routeName): void
    {
        Order::factory()->withStatus(OrderStatus::Live)->create();

        $this->actingAs(User::factory()->admin()->create())->get(route($routeName))->assertOk();
    }

    public function test_guests_are_sent_to_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_admin_creates_a_template_with_a_screenshot(): void
    {
        Storage::fake('public');

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.site-templates.store'), [
                'name' => 'Bistro',
                'slug' => 'bistro',
                'industry' => 'Restaurant',
                'summary' => 'For restaurants.',
                'description' => 'A warm design.',
                'features' => "Online menu\n\n  Reservations  \n",
                'demo_url' => 'https://demo.gulraiz.ca/bistro',
                'thumbnail' => UploadedFile::fake()->image('bistro.jpg', 1600, 1000),
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.site-templates.index'));

        $template = SiteTemplate::sole();
        $this->assertSame(['Online menu', 'Reservations'], $template->features);
        $this->assertTrue($template->is_active);
        Storage::disk('public')->assertExists($template->thumbnail_path);
    }

    public function test_template_slugs_must_be_unique(): void
    {
        SiteTemplate::factory()->create(['slug' => 'bistro']);

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.site-templates.store'), [
                'name' => 'Bistro 2',
                'slug' => 'bistro',
                'industry' => 'Restaurant',
                'summary' => 'x',
                'description' => 'x',
            ])
            ->assertSessionHasErrors('slug');
    }

    public function test_admin_can_hide_a_template(): void
    {
        $template = SiteTemplate::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->put(route('admin.site-templates.update', $template), [
                ...$template->only(['name', 'slug', 'industry', 'summary', 'description']),
                'is_active' => '0',
            ])
            ->assertRedirect(route('admin.site-templates.index'));

        $this->assertFalse($template->fresh()->is_active);
    }

    public function test_changing_order_status_emails_the_client(): void
    {
        Notification::fake();
        $order = Order::factory()->withStatus(OrderStatus::Building)->create();

        $this->actingAs(User::factory()->admin()->create())
            ->put(route('admin.orders.update', $order), [
                'status' => OrderStatus::ClientReview->value,
                'live_url' => 'https://preview.gulraiz.ca/maple',
                'checklist' => ['domain_registered', 'site_deployed'],
                'admin_notes' => 'Client prefers navy.',
            ])
            ->assertRedirect(route('admin.orders.show', $order));

        $order->refresh();
        $this->assertSame(OrderStatus::ClientReview, $order->status);
        $this->assertSame(['domain_registered', 'site_deployed'], $order->checklist);
        Notification::assertSentTo($order->user, OrderStatusChanged::class);
    }

    public function test_saving_an_order_without_changing_status_does_not_email_the_client(): void
    {
        Notification::fake();
        $order = Order::factory()->withStatus(OrderStatus::Building)->create();

        $this->actingAs(User::factory()->admin()->create())
            ->put(route('admin.orders.update', $order), ['status' => OrderStatus::Building->value, 'admin_notes' => 'Started']);

        Notification::assertNothingSent();
    }

    public function test_admin_cannot_set_an_order_back_to_pending_payment_or_use_unknown_checklist_items(): void
    {
        $order = Order::factory()->withStatus(OrderStatus::Building)->create();

        $this->actingAs(User::factory()->admin()->create())
            ->put(route('admin.orders.update', $order), [
                'status' => OrderStatus::PendingPayment->value,
                'checklist' => ['hack'],
            ])
            ->assertSessionHasErrors(['status', 'checklist.0']);
    }

    public function test_marking_an_edit_request_done_records_when(): void
    {
        $editRequest = EditRequest::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->put(route('admin.edit-requests.update', $editRequest), ['status' => EditRequestStatus::Done->value])
            ->assertRedirect();

        $editRequest->refresh();
        $this->assertSame(EditRequestStatus::Done, $editRequest->status);
        $this->assertNotNull($editRequest->completed_at);
    }

    public function test_opening_a_contact_message_marks_it_read(): void
    {
        $message = ContactMessage::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.contact-messages.show', $message))
            ->assertOk()
            ->assertSee($message->email);

        $this->assertNotNull($message->fresh()->read_at);
    }

    public function test_admin_updates_the_about_page(): void
    {
        Storage::fake('public');

        $this->actingAs(User::factory()->admin()->create())
            ->put(route('admin.about.update'), [
                'about_heading' => 'Hi, I’m Mohammed.',
                'about_body' => "First paragraph.\n\nSecond paragraph.",
                'about_photo' => UploadedFile::fake()->image('me.jpg'),
            ])
            ->assertRedirect(route('admin.about.edit'));

        Storage::disk('public')->assertExists(Setting::get('about_photo_path'));

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('Hi, I’m Mohammed.')
            ->assertSee('Second paragraph.');
    }

    public function test_admin_downloads_files_the_client_uploaded(): void
    {
        Storage::fake('local');
        $intake = IntakeSubmission::factory()->create(['logo_path' => 'orders/1/intake/logo.png']);
        Storage::disk('local')->put('orders/1/intake/logo.png', 'logo-bytes');

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.orders.file', [$intake->order, 'path' => 'orders/1/intake/logo.png']))
            ->assertOk()
            ->assertDownload('logo.png');
    }

    public function test_file_download_rejects_paths_not_attached_to_the_order(): void
    {
        Storage::fake('local');
        $intake = IntakeSubmission::factory()->create();
        Storage::disk('local')->put('orders/999/intake/secret.png', 'other client');

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.orders.file', [$intake->order, 'path' => 'orders/999/intake/secret.png']))
            ->assertNotFound();
    }
}
