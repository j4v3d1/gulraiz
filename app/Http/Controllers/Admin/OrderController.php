<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderRequest;
use App\Models\Order;
use App\Notifications\OrderStatusChanged;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = OrderStatus::tryFrom($request->string('status')->toString());

        return view('admin.orders.index', [
            'orders' => Order::query()
                ->with(['user', 'siteTemplate'])
                ->when($status, fn ($query) => $query->where('status', $status))
                ->latest()
                ->paginate(25)
                ->withQueryString(),
            'selectedStatus' => $status,
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'siteTemplate', 'intakeSubmission', 'editRequests' => fn ($query) => $query->latest()]);

        return view('admin.orders.show', ['order' => $order]);
    }

    public function update(UpdateOrderRequest $request, Order $order): RedirectResponse
    {
        $order->update([
            ...$request->safe()->only(['status', 'live_url', 'admin_notes']),
            'checklist' => $request->validated('checklist') ?? [],
        ]);

        if ($order->wasChanged('status')) {
            $order->user->notify(new OrderStatusChanged($order));
        }

        return to_route('admin.orders.show', $order)->with('status', __('Order updated.'));
    }
}
