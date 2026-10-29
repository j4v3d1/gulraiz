<?php

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Notifications\NewOrderReceived;
use App\Notifications\OrderConfirmed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class MarkOrderAsPaid
{
    /**
     * Record a completed checkout. Stripe may deliver the same event more
     * than once, so only the first delivery changes anything.
     */
    public function handle(Order $order, string $checkoutSessionId): void
    {
        $wasMarkedPaid = DB::transaction(function () use ($order, $checkoutSessionId): bool {
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($order->status !== OrderStatus::PendingPayment) {
                return false;
            }

            $order->forceFill([
                'status' => OrderStatus::AwaitingIntake,
                'stripe_checkout_session_id' => $checkoutSessionId,
                'paid_at' => now(),
                'commitment_ends_at' => $order->plan->hasMinimumCommitment()
                    ? now()->addMonths(config('gulraiz.commitment_months'))
                    : null,
            ])->save();

            return true;
        });

        if (! $wasMarkedPaid) {
            return;
        }

        $order->refresh();

        $order->user->notify(new OrderConfirmed($order));
        Notification::route('mail', config('gulraiz.admin_email'))->notify(new NewOrderReceived($order));
    }
}
