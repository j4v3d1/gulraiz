<x-layouts.admin :title="__('Dashboard')">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            [__('Waiting for client details'), $ordersByStatus[\App\Enums\OrderStatus::AwaitingIntake->value] ?? 0, route('admin.orders.index', ['status' => 'awaiting_intake'])],
            [__('To build'), $ordersByStatus[\App\Enums\OrderStatus::Building->value] ?? 0, route('admin.orders.index', ['status' => 'building'])],
            [__('Open edit requests'), $openEditRequests, route('admin.edit-requests.index')],
            [__('Unread messages'), $unreadMessages, route('admin.contact-messages.index')],
        ] as [$label, $count, $url])
            <a href="{{ $url }}" class="card p-5 hover:ring-brand-500">
                <p class="text-sm text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-3xl font-bold text-ink">{{ $count }}</p>
            </a>
        @endforeach
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="card p-5"><p class="text-sm text-slate-500">{{ __('In client review') }}</p><p class="mt-1 text-2xl font-bold text-ink">{{ $ordersByStatus[\App\Enums\OrderStatus::ClientReview->value] ?? 0 }}</p></div>
        <div class="card p-5"><p class="text-sm text-slate-500">{{ __('Live sites') }}</p><p class="mt-1 text-2xl font-bold text-ink">{{ $ordersByStatus[\App\Enums\OrderStatus::Live->value] ?? 0 }}</p></div>
        <div class="card p-5"><p class="text-sm text-slate-500">{{ __('Minimum terms ending in 30 days') }}</p><p class="mt-1 text-2xl font-bold text-ink">{{ $commitmentsEndingSoon }}</p></div>
    </div>

    <div class="card overflow-hidden">
        <h2 class="border-b border-slate-100 px-5 py-4 font-semibold text-ink">{{ __('Recent orders') }}</h2>
        @include('admin.orders.table', ['orders' => $recentOrders])
    </div>
</x-layouts.admin>
