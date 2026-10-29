<?php

namespace App\Actions;

use App\Models\Order;
use Carbon\CarbonInterface;

class CancelOrderSubscription
{
    /**
     * Cancel the order's subscription. Inside the minimum commitment the
     * cancellation is scheduled for the end of the term, so the client
     * keeps paying until then; otherwise it ends with the current period.
     *
     * Returns the date the subscription will end.
     */
    public function handle(Order $order): CarbonInterface
    {
        $subscription = $order->subscription();

        abort_if($subscription === null, 409, __('This order has no active subscription.'));

        $subscription = $order->isWithinCommitment()
            ? $subscription->cancelAt($order->commitment_ends_at)
            : $subscription->cancel();

        $order->forceFill(['cancellation_requested_at' => now()])->save();

        return $subscription->ends_at;
    }
}
