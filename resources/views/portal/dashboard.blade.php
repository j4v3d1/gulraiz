<x-layouts.app :title="__('My account')">
    <section class="mx-auto max-w-5xl px-4 py-12 sm:px-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-ink">{{ __('Hi :name', ['name' => str(auth()->user()->name)->before(' ')]) }}</h1>
                <p class="mt-1 text-slate-600">{{ __('Here are your websites and their status.') }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('portal.billing') }}" class="btn-secondary">{{ __('Billing & invoices') }}</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-secondary">{{ __('Log out') }}</button>
                </form>
            </div>
        </div>

        @if ($orders->isEmpty())
            <div class="card mt-10 p-10 text-center">
                <h2 class="text-lg font-semibold text-ink">{{ __('You don’t have a website with us yet') }}</h2>
                <p class="mt-2 text-slate-600">{{ __('Pick a design to get started.') }}</p>
                <a href="{{ route('templates.index') }}" class="btn-primary mt-6">{{ __('Browse designs') }}</a>
            </div>
        @else
            <ul role="list" class="mt-10 space-y-4">
                @foreach ($orders as $order)
                    <li class="card flex flex-wrap items-center justify-between gap-4 p-5">
                        <div>
                            <p class="font-semibold text-ink">{{ $order->intakeSubmission?->business_name ?? $order->siteTemplate?->name ?? __('Website') }}</p>
                            <p class="text-sm text-slate-500">
                                {{ $order->siteTemplate?->name }} &middot; {{ $order->plan->label() }} &middot; {{ __('Order #:id', ['id' => $order->id]) }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <x-badge :status="$order->status" />
                            @if ($order->status !== \App\Enums\OrderStatus::PendingPayment)
                                <a href="{{ route('portal.orders.show', $order) }}" class="btn-secondary">{{ __('View') }}</a>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</x-layouts.app>
