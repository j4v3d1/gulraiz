<?php

namespace Tests\Feature;

use App\Enums\EditRequestStatus;
use App\Enums\OrderStatus;
use App\Models\EditRequest;
use App\Models\IntakeSubmission;
use App\Models\Order;
use App\Models\User;
use App\Notifications\EditRequestSubmitted;
use App\Notifications\IntakeSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClientPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_lists_only_the_clients_own_orders(): void
    {
        $user = User::factory()->create();
        Order::factory()->for($user)->paid()->create(['site_template_id' => null]);
        $otherOrder = Order::factory()->paid()->create();

        $this->actingAs($user)
            ->get(route('portal.dashboard'))
            ->assertOk()
            ->assertSee('Waiting for your details')
            ->assertDontSee($otherOrder->siteTemplate->name);
    }

    public function test_client_cannot_view_someone_elses_order(): void
    {
        $order = Order::factory()->paid()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('portal.orders.show', $order))
            ->assertForbidden();
    }

    public function test_paid_order_prompts_the_client_for_their_details(): void
    {
        $order = Order::factory()->paid()->create();

        $this->actingAs($order->user)
            ->get(route('portal.orders.show', $order))
            ->assertOk()
            ->assertSee(route('portal.orders.intake.create', $order), escape: false);
    }

    public function test_client_submits_the_intake_form_with_files(): void
    {
        Storage::fake('local');
        Notification::fake();
        $order = Order::factory()->paid()->create();

        $this->actingAs($order->user)
            ->post(route('portal.orders.intake.store', $order), [
                'business_name' => 'Maple Bakery',
                'contact_email' => 'hello@maplebakery.ca',
                'desired_domain' => 'maplebakery.ca',
                'about_text' => 'Family bakery since 1998.',
                'logo' => UploadedFile::fake()->image('logo.png'),
                'photos' => [UploadedFile::fake()->image('shop.jpg'), UploadedFile::fake()->image('bread.jpg')],
            ])
            ->assertRedirect(route('portal.orders.show', $order));

        $order->refresh();
        $intake = $order->intakeSubmission;
        $this->assertSame(OrderStatus::Building, $order->status);
        $this->assertSame('Maple Bakery', $intake->business_name);
        Storage::disk('local')->assertExists($intake->logo_path);
        $this->assertCount(2, $intake->photo_paths);
        Storage::disk('local')->assertExists($intake->photo_paths);
        Notification::assertSentOnDemand(IntakeSubmitted::class);
    }

    public function test_intake_form_rejects_non_image_logos(): void
    {
        Storage::fake('local');
        $order = Order::factory()->paid()->create();

        $this->actingAs($order->user)
            ->post(route('portal.orders.intake.store', $order), [
                'business_name' => 'Maple Bakery',
                'contact_email' => 'hello@maplebakery.ca',
                'about_text' => 'Family bakery.',
                'logo' => UploadedFile::fake()->create('logo.php', 10, 'application/x-php'),
            ])
            ->assertSessionHasErrors('logo');

        $this->assertDatabaseCount('intake_submissions', 0);
    }

    public function test_intake_form_cannot_be_submitted_twice(): void
    {
        $intake = IntakeSubmission::factory()->create();
        $intake->order->update(['status' => OrderStatus::AwaitingIntake]);

        $this->actingAs($intake->order->user)
            ->get(route('portal.orders.intake.create', $intake->order))
            ->assertForbidden();
    }

    public function test_client_cannot_submit_intake_for_someone_elses_order(): void
    {
        $order = Order::factory()->paid()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('portal.orders.intake.store', $order), [
                'business_name' => 'Hijack',
                'contact_email' => 'x@example.com',
                'about_text' => 'x',
            ])
            ->assertForbidden();
    }

    public function test_client_sends_an_edit_request_for_a_live_site(): void
    {
        Storage::fake('local');
        Notification::fake();
        $order = Order::factory()->withStatus(OrderStatus::Live)->create();

        $this->actingAs($order->user)
            ->post(route('portal.orders.edit-requests.store', $order), [
                'description' => 'Change Saturday hours to 9–3.',
                'attachments' => [UploadedFile::fake()->create('menu.pdf', 100, 'application/pdf')],
            ])
            ->assertRedirect(route('portal.orders.show', $order));

        $editRequest = EditRequest::sole();
        $this->assertSame(EditRequestStatus::Open, $editRequest->status);
        Storage::disk('local')->assertExists($editRequest->attachment_paths[0]);
        Notification::assertSentOnDemand(EditRequestSubmitted::class);
    }

    public function test_edit_requests_are_limited_per_month(): void
    {
        config(['gulraiz.monthly_edit_limit' => 10]);
        $order = Order::factory()->withStatus(OrderStatus::Live)->create();
        EditRequest::factory()->count(10)->for($order)->create();

        $this->actingAs($order->user)
            ->post(route('portal.orders.edit-requests.store', $order), ['description' => 'One more change'])
            ->assertSessionHasErrors('description');

        $this->assertSame(10, $order->editRequests()->count());
    }

    public function test_last_months_edit_requests_do_not_count_towards_the_limit(): void
    {
        Notification::fake();
        config(['gulraiz.monthly_edit_limit' => 10]);
        $order = Order::factory()->withStatus(OrderStatus::Live)->create();
        EditRequest::factory()->count(10)->for($order)->create(['created_at' => now()->subMonthNoOverflow()->startOfMonth()]);

        $this->actingAs($order->user)
            ->post(route('portal.orders.edit-requests.store', $order), ['description' => 'New month, new change'])
            ->assertSessionHasNoErrors();

        $this->assertSame(11, $order->editRequests()->count());
    }

    public function test_edit_requests_are_not_accepted_while_the_site_is_being_built(): void
    {
        $order = Order::factory()->withStatus(OrderStatus::Building)->create();

        $this->actingAs($order->user)
            ->post(route('portal.orders.edit-requests.store', $order), ['description' => 'Too early'])
            ->assertForbidden();
    }
}
