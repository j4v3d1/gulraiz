<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use App\Notifications\NewOrderReceived;
use App\Notifications\OrderConfirmed;
use App\Notifications\PaymentFailed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['cashier.webhook.secret' => null]);
        Notification::fake();
    }

    public function test_completed_checkout_marks_a_monthly_order_paid_with_a_twelve_month_minimum(): void
    {
        $this->freezeSecond();
        $order = $this->pendingOrder();

        $this->sendCheckoutCompleted($order)->assertOk();

        $order->refresh();
        $this->assertSame(OrderStatus::AwaitingIntake, $order->status);
        $this->assertSame('cs_test_123', $order->stripe_checkout_session_id);
        $this->assertTrue($order->paid_at->equalTo(now()));
        $this->assertTrue($order->commitment_ends_at->equalTo(now()->addMonths(12)));

        Notification::assertSentTo($order->user, OrderConfirmed::class);
        Notification::assertSentOnDemand(NewOrderReceived::class);
    }

    public function test_yearly_orders_have_no_minimum_term(): void
    {
        $order = $this->pendingOrder(yearly: true);

        $this->sendCheckoutCompleted($order)->assertOk();

        $order->refresh();
        $this->assertSame(OrderStatus::AwaitingIntake, $order->status);
        $this->assertNull($order->commitment_ends_at);
    }

    public function test_repeated_delivery_of_the_same_event_only_notifies_once(): void
    {
        $order = $this->pendingOrder();

        $this->sendCheckoutCompleted($order);
        $this->sendCheckoutCompleted($order);

        Notification::assertSentToTimes($order->user, OrderConfirmed::class, 1);
        Notification::assertSentOnDemandTimes(NewOrderReceived::class, 1);
    }

    public function test_checkout_for_a_different_stripe_customer_is_ignored(): void
    {
        $order = $this->pendingOrder();

        $this->sendCheckoutCompleted($order, customer: 'cus_someone_else')->assertOk();

        $this->assertSame(OrderStatus::PendingPayment, $order->fresh()->status);
        Notification::assertNothingSent();
    }

    public function test_failed_payment_notifies_the_admin(): void
    {
        $order = $this->paidOrderWithSubscription();

        $this->postJson('/stripe/webhook', [
            'type' => 'invoice.payment_failed',
            'data' => ['object' => [
                'customer' => $order->user->stripe_id,
                'parent' => ['subscription_details' => ['subscription' => 'sub_123']],
            ]],
        ])->assertOk();

        Notification::assertSentOnDemand(
            PaymentFailed::class,
            fn (PaymentFailed $notification) => $notification->order->is($order),
        );
    }

    public function test_deleted_subscription_cancels_the_order(): void
    {
        $order = $this->paidOrderWithSubscription();

        $this->postJson('/stripe/webhook', [
            'type' => 'customer.subscription.deleted',
            'data' => ['object' => ['id' => 'sub_123', 'customer' => $order->user->stripe_id]],
        ])->assertOk();

        $this->assertSame(OrderStatus::Cancelled, $order->fresh()->status);
    }

    private function pendingOrder(bool $yearly = false): Order
    {
        $user = User::factory()->create(['stripe_id' => 'cus_123']);
        $factory = Order::factory()->for($user);

        $order = ($yearly ? $factory->yearly() : $factory)->create();
        $order->forceFill(['subscription_type' => "order-{$order->id}"])->save();

        return $order;
    }

    private function paidOrderWithSubscription(): Order
    {
        $user = User::factory()->create(['stripe_id' => 'cus_123']);
        $order = Order::factory()->for($user)->withStatus(OrderStatus::Live)->create();

        $user->subscriptions()->create([
            'type' => $order->subscription_type,
            'stripe_id' => 'sub_123',
            'stripe_status' => 'active',
            'stripe_price' => 'price_monthly',
            'quantity' => 1,
        ]);

        return $order;
    }

    private function sendCheckoutCompleted(Order $order, string $customer = 'cus_123'): TestResponse
    {
        return $this->postJson('/stripe/webhook', [
            'type' => 'checkout.session.completed',
            'data' => ['object' => [
                'id' => 'cs_test_123',
                'mode' => 'subscription',
                'customer' => $customer,
                'metadata' => ['order_id' => (string) $order->id],
            ]],
        ]);
    }
}
