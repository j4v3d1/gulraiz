<?php

namespace App\Actions;

use App\Models\Order;

class CreateCheckoutSession
{
    /**
     * Start a Stripe Checkout session for the order's subscription and
     * return the URL to send the client to.
     */
    public function handle(Order $order): string
    {
        $checkout = $order->user
            ->newSubscription($order->subscription_type, $order->plan->stripePriceId())
            ->withMetadata(['order_id' => $order->id])
            ->checkout([
                'success_url' => route('portal.orders.show', $order).'?checkout=success',
                'cancel_url' => route('templates.show', $order->siteTemplate),
                'client_reference_id' => (string) $order->id,
                'metadata' => ['order_id' => $order->id],
                'automatic_tax' => ['enabled' => true],
                'billing_address_collection' => 'required',
                'customer_update' => ['address' => 'auto', 'name' => 'auto'],
            ]);

        return $checkout->asStripeCheckoutSession()->url;
    }
}
