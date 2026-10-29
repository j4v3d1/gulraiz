<?php

namespace App\Listeners;

use App\Actions\MarkOrderAsPaid;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Notifications\PaymentFailed;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;
use Laravel\Cashier\Events\WebhookHandled;
use Laravel\Cashier\Events\WebhookReceived;
use Laravel\Cashier\Subscription;

class HandleStripeWebhook
{
    public function __construct(private MarkOrderAsPaid $markOrderAsPaid) {}

    /**
     * Checkout completion is handled on receipt; subscription events are
     * handled after Cashier has synced its own subscription records.
     */
    public function handle(WebhookReceived|WebhookHandled $event): void
    {
        $type = $event->payload['type'] ?? null;
        $object = $event->payload['data']['object'] ?? [];

        match (true) {
            $event instanceof WebhookReceived && $type === 'checkout.session.completed' => $this->handleCheckoutCompleted($object),
            $event instanceof WebhookHandled && $type === 'customer.subscription.deleted' => $this->handleSubscriptionDeleted($object),
            $event instanceof WebhookReceived && $type === 'invoice.payment_failed' => $this->handlePaymentFailed($object),
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $session
     */
    private function handleCheckoutCompleted(array $session): void
    {
        if (($session['mode'] ?? null) !== 'subscription') {
            return;
        }

        $order = Order::find($session['metadata']['order_id'] ?? null);

        if ($order === null || $order->user->stripe_id !== ($session['customer'] ?? null)) {
            return;
        }

        $this->markOrderAsPaid->handle($order, $session['id']);
    }

    /**
     * @param  array<string, mixed>  $subscription
     */
    private function handleSubscriptionDeleted(array $subscription): void
    {
        $order = $this->findOrderForSubscription($subscription['id'] ?? null);

        $order?->forceFill(['status' => OrderStatus::Cancelled])->save();
    }

    /**
     * @param  array<string, mixed>  $invoice
     */
    private function handlePaymentFailed(array $invoice): void
    {
        $subscriptionId = $invoice['subscription']
            ?? Arr::get($invoice, 'parent.subscription_details.subscription');

        $order = $this->findOrderForSubscription($subscriptionId);

        if ($order === null) {
            return;
        }

        Notification::route('mail', config('gulraiz.admin_email'))->notify(new PaymentFailed($order));
    }

    private function findOrderForSubscription(?string $stripeSubscriptionId): ?Order
    {
        if ($stripeSubscriptionId === null) {
            return null;
        }

        $subscription = Subscription::query()->where('stripe_id', $stripeSubscriptionId)->first();

        if ($subscription === null) {
            return null;
        }

        return Order::query()
            ->where('user_id', $subscription->user_id)
            ->where('subscription_type', $subscription->type)
            ->first();
    }
}
