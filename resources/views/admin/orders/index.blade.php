<x-layouts.admin :title="__('Orders')">
    <nav class="flex flex-wrap gap-2" aria-label="{{ __('Filter by status') }}">
        <a href="{{ route('admin.orders.index') }}" @class(['rounded-full px-3 py-1 text-sm font-medium ring-1 ring-inset', 'bg-brand-700 text-white ring-brand-700' => ! $selectedStatus, 'bg-white ring-slate-300' => $selectedStatus])>{{ __('All') }}</a>
        @foreach (\App\Enums\OrderStatus::cases() as $status)
            <a href="{{ route('admin.orders.index', ['status' => $status->value]) }}" @class(['rounded-full px-3 py-1 text-sm font-medium ring-1 ring-inset', 'bg-brand-700 text-white ring-brand-700' => $selectedStatus === $status, 'bg-white ring-slate-300' => $selectedStatus !== $status])>{{ $status->label() }}</a>
        @endforeach
    </nav>
    <div class="card overflow-hidden">
        @include('admin.orders.table', ['orders' => $orders])
    </div>
    {{ $orders->links() }}
</x-layouts.admin>
