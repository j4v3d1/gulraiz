<?php

namespace Tests\Feature;

use App\Actions\CreateCheckoutSession;
use App\Enums\OrderStatus;
use App\Enums\Plan;
use App\Models\Order;
use App\Models\SiteTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_must_log_in_before_checkout(): void
    {
        $template = SiteTemplate::factory()->create();

        $this->get(route('checkout.create', $template))->assertRedirect(route('login'));
    }

    public function test_checkout_page_shows_both_plans_and_the_minimum_term(): void
    {
        $template = SiteTemplate::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('checkout.create', [$template, 'plan' => 'yearly']))
            ->assertOk()
            ->assertSee('$50')
            ->assertSee('$500')
            ->assertSee('12-month minimum')
            ->assertSee('value="yearly" class="mt-1 size-4 accent-brand-700" checked', escape: false);
    }

    public function test_starting_checkout_creates_a_pending_order_and_redirects_to_stripe(): void
    {
        $user = User::factory()->create();
        $template = SiteTemplate::factory()->create();

        $this->mock(CreateCheckoutSession::class, function (MockInterface $mock): void {
            $mock->shouldReceive('handle')->once()->andReturn('https://checkout.stripe.com/c/pay/cs_test_123');
        });

        $this->actingAs($user)
            ->post(route('checkout.store', $template), ['plan' => 'monthly', 'accept_terms' => '1'])
            ->assertRedirect('https://checkout.stripe.com/c/pay/cs_test_123');

        $order = Order::sole();
        $this->assertTrue($order->user->is($user));
        $this->assertTrue($order->siteTemplate->is($template));
        $this->assertSame(Plan::Monthly, $order->plan);
        $this->assertSame(OrderStatus::PendingPayment, $order->status);
        $this->assertSame("order-{$order->id}", $order->subscription_type);
        $this->assertNotNull($order->commitment_accepted_at);
        $this->assertNull($order->paid_at);
    }

    public function test_client_must_accept_the_terms(): void
    {
        $template = SiteTemplate::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('checkout.store', $template), ['plan' => 'monthly'])
            ->assertSessionHasErrors('accept_terms');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_plan_must_be_monthly_or_yearly(): void
    {
        $template = SiteTemplate::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('checkout.store', $template), ['plan' => 'lifetime', 'accept_terms' => '1'])
            ->assertSessionHasErrors('plan');
    }

    public function test_hidden_templates_cannot_be_bought(): void
    {
        $template = SiteTemplate::factory()->inactive()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('checkout.store', $template), ['plan' => 'monthly', 'accept_terms' => '1'])
            ->assertNotFound();

        $this->assertDatabaseCount('orders', 0);
    }
}
