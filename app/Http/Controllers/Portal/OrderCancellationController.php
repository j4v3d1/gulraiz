<?php

namespace App\Http\Controllers\Portal;

use App\Actions\CancelOrderSubscription;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class OrderCancellationController extends Controller
{
    public function store(Order $order, CancelOrderSubscription $cancelOrderSubscription): RedirectResponse
    {
        Gate::authorize('cancel', $order);

        $endsAt = $cancelOrderSubscription->handle($order);

        return to_route('portal.orders.show', $order)->with('status', __('Your subscription is cancelled and will end on :date. Your website stays online until then.', [
            'date' => $endsAt->toFormattedDateString(),
        ]));
    }
}
