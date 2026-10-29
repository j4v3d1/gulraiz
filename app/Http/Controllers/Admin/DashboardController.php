<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EditRequestStatus;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\EditRequest;
use App\Models\Order;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $ordersByStatus = Order::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.dashboard', [
            'ordersByStatus' => $ordersByStatus,
            'openEditRequests' => EditRequest::query()->where('status', '!=', EditRequestStatus::Done)->count(),
            'unreadMessages' => ContactMessage::query()->whereNull('read_at')->count(),
            'commitmentsEndingSoon' => Order::query()
                ->where('status', '!=', OrderStatus::Cancelled)
                ->whereBetween('commitment_ends_at', [now(), now()->addDays(30)])
                ->count(),
            'recentOrders' => Order::query()
                ->with(['user', 'siteTemplate'])
                ->where('status', '!=', OrderStatus::PendingPayment)
                ->latest('paid_at')
                ->limit(8)
                ->get(),
        ]);
    }
}
