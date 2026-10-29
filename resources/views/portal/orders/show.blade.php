<x-layouts.app :title="__('Order #:id', ['id' => $order->id])">
    <section class="mx-auto max-w-5xl px-4 py-12 sm:px-6">
        <a href="{{ route('portal.dashboard') }}" class="link text-sm">&larr; {{ __('My account') }}</a>

        @if (request('checkout') === 'success' && $order->status === \App\Enums\OrderStatus::PendingPayment)
            <div class="mt-6 rounded-lg bg-sky-50 px-4 py-3 text-sm text-sky-800 ring-1 ring-sky-200">{{ __('Thanks! We’re confirming your payment with Stripe. This usually takes a few seconds, so refresh the page shortly.') }}</div>
        @endif

        <div class="mt-4 flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-ink">{{ $order->intakeSubmission?->business_name ?? $order->siteTemplate?->name ?? __('Your website') }}</h1>
                <p class="mt-1 text-slate-500">{{ $order->siteTemplate?->name }} &middot; {{ __('Order #:id', ['id' => $order->id]) }}</p>
            </div>
            <x-badge :status="$order->status" class="text-sm" />
        </div>

        @php
            $steps = [
                \App\Enums\OrderStatus::AwaitingIntake,
                \App\Enums\OrderStatus::Building,
                \App\Enums\OrderStatus::ClientReview,
                \App\Enums\OrderStatus::Live,
            ];
            $currentIndex = array_search($order->status, $steps, true);
        @endphp
        @if ($currentIndex !== false)
            <ol class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-4">
                @foreach ($steps as $index => $step)
                    <li @class([
                        'rounded-xl px-4 py-3 text-sm font-medium ring-1',
                        'bg-brand-700 text-white ring-brand-700' => $index === $currentIndex,
                        'bg-emerald-50 text-emerald-800 ring-emerald-200' => $index < $currentIndex,
                        'bg-white text-slate-400 ring-slate-200' => $index > $currentIndex,
                    ]) @if ($index === $currentIndex) aria-current="step" @endif>
                        <span class="block text-xs opacity-75">{{ __('Step :n', ['n' => $index + 1]) }}</span>
                        {{ $step->label() }}
                    </li>
                @endforeach
            </ol>
        @endif

        <div class="mt-8 grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                @can('submitIntake', $order)
                    <div class="card border-l-4 border-amber-400 p-6">
                        <h2 class="text-lg font-semibold text-ink">{{ __('Next step: tell me about your business') }}</h2>
                        <p class="mt-2 text-slate-600">{{ __('Send your business details, logo and photos so I can start building.') }}</p>
                        <a href="{{ route('portal.orders.intake.create', $order) }}" class="btn-primary mt-4">{{ __('Fill in your details') }}</a>
                    </div>
                @endcan

                @if ($order->status === \App\Enums\OrderStatus::Building)
                    <div class="card p-6">
                        <h2 class="text-lg font-semibold text-ink">{{ __('Your website is being built') }}</h2>
                        <p class="mt-2 text-slate-600">{{ __('I have your details and I’m working on it. You’ll get an email when it’s ready to review.') }}</p>
                    </div>
                @endif

                @if ($order->live_url)
                    <div class="card p-6">
                        <h2 class="text-lg font-semibold text-ink">{{ $order->status === \App\Enums\OrderStatus::Live ? __('Your website is live') : __('Preview your website') }}</h2>
                        <a href="{{ $order->live_url }}" target="_blank" rel="noopener noreferrer" class="link mt-2 inline-block break-all">{{ $order->live_url }} &nearr;</a>
                    </div>
                @endif

                @if ($order->acceptsEditRequests())
                    <div class="card p-6">
                        <div class="flex flex-wrap items-baseline justify-between gap-2">
                            <h2 class="text-lg font-semibold text-ink">{{ __('Request an edit') }}</h2>
                            <p class="text-sm text-slate-500">{{ __(':remaining of :limit left this month', ['remaining' => $order->editRequestsRemainingThisMonth(), 'limit' => $editLimit]) }}</p>
                        </div>
                        @can('requestEdit', $order)
                            <form method="POST" action="{{ route('portal.orders.edit-requests.store', $order) }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                                @csrf
                                <x-field name="description" type="textarea" :label="__('What would you like changed?')" required :hint="__('Be as specific as you can, e.g. “Change Saturday hours to 9–3”.')" />
                                <x-field name="attachments[]" id="attachments" type="file" :label="__('Attachments')" multiple accept=".jpg,.jpeg,.png,.webp,.pdf,.docx" :hint="__('Up to 5 files: images, PDF or Word, 10 MB each.')" />
                                <button type="submit" class="btn-primary" @disabled($order->editRequestsRemainingThisMonth() === 0)>{{ __('Send request') }}</button>
                            </form>
                        @endcan

                        @if ($order->editRequests->isNotEmpty())
                            <h3 class="mt-8 text-sm font-semibold text-ink">{{ __('Previous requests') }}</h3>
                            <ul role="list" class="mt-3 divide-y divide-slate-100">
                                @foreach ($order->editRequests as $editRequest)
                                    <li class="flex items-start justify-between gap-4 py-3">
                                        <div class="min-w-0">
                                            <p class="text-sm whitespace-pre-line text-slate-700">{{ str($editRequest->description)->limit(200) }}</p>
                                            <p class="mt-1 text-xs text-slate-500">{{ $editRequest->created_at->toFormattedDateString() }}</p>
                                        </div>
                                        <x-badge :status="$editRequest->status" />
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif
            </div>

            <aside class="space-y-6">
                <div class="card p-6">
                    <h2 class="font-semibold text-ink">{{ __('Subscription') }}</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between gap-4"><dt class="text-slate-500">{{ __('Plan') }}</dt><dd class="font-medium text-ink">{{ $order->plan->formattedPrice() }} / {{ $order->plan->intervalLabel() }} + {{ __('tax') }}</dd></div>
                        @if ($order->paid_at)
                            <div class="flex justify-between gap-4"><dt class="text-slate-500">{{ __('Started') }}</dt><dd class="text-ink">{{ $order->paid_at->toFormattedDateString() }}</dd></div>
                        @endif
                        @if ($order->commitment_ends_at)
                            <div class="flex justify-between gap-4"><dt class="text-slate-500">{{ __('Minimum term ends') }}</dt><dd class="text-ink">{{ $order->commitment_ends_at->toFormattedDateString() }}</dd></div>
                        @endif
                        @if ($order->cancellation_requested_at)
                            <div class="rounded-lg bg-rose-50 px-3 py-2 text-rose-800">{{ __('Cancellation requested on :date.', ['date' => $order->cancellation_requested_at->toFormattedDateString()]) }}</div>
                        @endif
                    </dl>
                    <a href="{{ route('portal.billing') }}" class="btn-secondary mt-5 w-full">{{ __('Update card & invoices') }}</a>
                </div>

                @can('cancel', $order)
                    <details class="card p-6">
                        <summary class="cursor-pointer list-none text-sm font-medium text-slate-500 hover:text-ink">{{ __('Cancel subscription') }}</summary>
                        <div class="mt-4 space-y-3 text-sm text-slate-600">
                            @if ($order->isWithinCommitment())
                                <p>{{ __('You’re within your :months-month minimum. If you cancel now, billing continues until :date and your subscription ends then.', ['months' => config('gulraiz.commitment_months'), 'date' => $order->commitment_ends_at->toFormattedDateString()]) }}</p>
                            @else
                                <p>{{ __('Your subscription will end at the end of the current billing period.') }}</p>
                            @endif
                            <form method="POST" action="{{ route('portal.orders.cancellation.store', $order) }}">
                                @csrf
                                <button type="submit" class="btn-danger w-full">{{ __('Yes, cancel my subscription') }}</button>
                            </form>
                        </div>
                    </details>
                @endcan
            </aside>
        </div>
    </section>
</x-layouts.app>
