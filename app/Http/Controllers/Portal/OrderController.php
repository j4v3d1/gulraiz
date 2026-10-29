<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function show(Order $order): View
    {
        Gate::authorize('view', $order);

        $order->load(['siteTemplate', 'intakeSubmission', 'editRequests' => fn ($query) => $query->latest()]);

        return view('portal.orders.show', [
            'order' => $order,
            'editLimit' => config('gulraiz.monthly_edit_limit'),
        ]);
    }
}
