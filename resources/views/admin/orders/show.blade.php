<x-layouts.admin :title="__('Order #:id', ['id' => $order->id])">
    <x-slot:actions><x-badge :status="$order->status" class="text-sm" /></x-slot:actions>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="card space-y-5 p-6">
                @csrf
                @method('PUT')
                <h2 class="font-semibold text-ink">{{ __('Build progress') }}</h2>
                @if ($order->status === \App\Enums\OrderStatus::PendingPayment)
                    <p class="rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-600">{{ __('Payment hasn’t been confirmed by Stripe yet.') }}</p>
                @else
                    <div>
                        <label for="status" class="form-label">{{ __('Status') }}</label>
                        <select id="status" name="status" class="form-input mt-1.5">
                            @foreach (\App\Enums\OrderStatus::adminSelectable() as $status)
                                <option value="{{ $status->value }}" @selected(old('status', $order->status->value) === $status->value)>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1.5 text-sm text-slate-500">{{ __('The client gets an email when the status changes. “Cancelled” here does not stop billing; cancel in Stripe.') }}</p>
                    </div>
                    <x-field name="live_url" type="url" :label="__('Site URL (preview or live)')" :value="$order->live_url" />
                    <fieldset>
                        <legend class="form-label">{{ __('Launch checklist') }}</legend>
                        <div class="mt-2 grid gap-2 sm:grid-cols-2">
                            @foreach (\App\Models\Order::CHECKLIST_ITEMS as $key => $label)
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="checklist[]" value="{{ $key }}" class="size-4 accent-brand-700" @checked(in_array($key, old('checklist', $order->checklist ?? []), true))>
                                    {{ __($label) }}
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                    <x-field name="admin_notes" type="textarea" :label="__('Private notes')" :value="$order->admin_notes" />
                    <button type="submit" class="btn-primary">{{ __('Save') }}</button>
                @endif
            </form>

            <div class="card p-6">
                <h2 class="font-semibold text-ink">{{ __('Intake form') }}</h2>
                @if ($intake = $order->intakeSubmission)
                    <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                        @foreach ([
                            __('Business name') => $intake->business_name,
                            __('Contact email') => $intake->contact_email,
                            __('Contact phone') => $intake->contact_phone,
                            __('Desired domain') => $intake->desired_domain,
                            __('Colours') => $intake->colour_preferences,
                        ] as $label => $value)
                            <div><dt class="text-slate-500">{{ $label }}</dt><dd class="font-medium text-ink">{{ $value ?: '—' }}</dd></div>
                        @endforeach
                    </dl>
                    @foreach ([__('About') => $intake->about_text, __('Services') => $intake->services_text, __('Other notes') => $intake->extra_notes] as $label => $value)
                        @if ($value)
                            <h3 class="mt-5 text-sm text-slate-500">{{ $label }}</h3>
                            <p class="mt-1 text-sm whitespace-pre-line text-ink">{{ $value }}</p>
                        @endif
                    @endforeach
                    @php($files = collect([$intake->logo_path])->merge($intake->photo_paths ?? [])->filter())
                    @if ($files->isNotEmpty())
                        <h3 class="mt-5 text-sm text-slate-500">{{ __('Files') }}</h3>
                        <ul class="mt-1 space-y-1 text-sm">
                            @foreach ($files as $path)
                                <li><a href="{{ route('admin.orders.file', [$order, 'path' => $path]) }}" class="link">{{ $path === $intake->logo_path ? __('Logo') : __('Photo :n', ['n' => $loop->index]) }}</a></li>
                            @endforeach
                        </ul>
                    @endif
                @else
                    <p class="mt-2 text-sm text-slate-500">{{ __('Not submitted yet.') }}</p>
                @endif
            </div>

            @if ($order->editRequests->isNotEmpty())
                <div class="card p-6">
                    <h2 class="font-semibold text-ink">{{ __('Edit requests') }}</h2>
                    <ul class="mt-3 divide-y divide-slate-100">
                        @foreach ($order->editRequests as $editRequest)
                            <li class="flex items-start justify-between gap-4 py-3 text-sm">
                                <div>
                                    <p class="whitespace-pre-line text-ink">{{ $editRequest->description }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $editRequest->created_at->toDayDateTimeString() }}</p>
                                </div>
                                <x-badge :status="$editRequest->status" />
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('admin.edit-requests.index') }}" class="link mt-2 inline-block text-sm">{{ __('Manage edit requests') }} &rarr;</a>
                </div>
            @endif
        </div>

        <aside class="space-y-6">
            <div class="card p-6 text-sm">
                <h2 class="font-semibold text-ink">{{ __('Client') }}</h2>
                <p class="mt-3 font-medium text-ink">{{ $order->user->name }}</p>
                <p><a href="mailto:{{ $order->user->email }}" class="link">{{ $order->user->email }}</a></p>
                @if ($order->user->phone)
                    <p class="text-slate-600">{{ $order->user->phone }}</p>
                @endif
            </div>
            <div class="card p-6 text-sm">
                <h2 class="font-semibold text-ink">{{ __('Subscription') }}</h2>
                <dl class="mt-3 space-y-2">
                    <div class="flex justify-between gap-2"><dt class="text-slate-500">{{ __('Template') }}</dt><dd>{{ $order->siteTemplate?->name ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-500">{{ __('Plan') }}</dt><dd>{{ $order->plan->label() }}</dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-500">{{ __('Paid') }}</dt><dd>{{ $order->paid_at?->toFormattedDateString() ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-500">{{ __('Minimum term ends') }}</dt><dd>{{ $order->commitment_ends_at?->toFormattedDateString() ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-500">{{ __('Terms accepted') }}</dt><dd>{{ $order->commitment_accepted_at->toDayDateTimeString() }}</dd></div>
                    @if ($order->cancellation_requested_at)
                        <div class="flex justify-between gap-2"><dt class="text-slate-500">{{ __('Cancellation requested') }}</dt><dd class="text-rose-700">{{ $order->cancellation_requested_at->toFormattedDateString() }}</dd></div>
                    @endif
                </dl>
                @if ($order->user->stripe_id)
                    <a href="https://dashboard.stripe.com/customers/{{ $order->user->stripe_id }}" target="_blank" rel="noopener noreferrer" class="link mt-4 inline-block">{{ __('Open in Stripe') }} &nearr;</a>
                @endif
            </div>
        </aside>
    </div>
</x-layouts.admin>
