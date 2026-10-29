<?php

namespace App\Policies;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->user_id;
    }

    public function submitIntake(User $user, Order $order): bool
    {
        return $this->view($user, $order) && $order->acceptsIntake();
    }

    public function requestEdit(User $user, Order $order): bool
    {
        return $this->view($user, $order) && $order->acceptsEditRequests();
    }

    public function cancel(User $user, Order $order): bool
    {
        return $this->view($user, $order)
            && $order->isPaid()
            && $order->cancellation_requested_at === null
            && $order->status !== OrderStatus::Cancelled;
    }
}
