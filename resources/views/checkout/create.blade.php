<x-layouts.app :title="__('Get the :name website', ['name' => $template->name])">
    <section class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
        <a href="{{ route('templates.show', $template) }}" class="link text-sm">&larr; {{ $template->name }}</a>
        <h1 class="mt-4 text-3xl font-bold tracking-tight text-ink">{{ __('Get the :name website', ['name' => $template->name]) }}</h1>
        <p class="mt-2 text-slate-600">{{ __('Choose how you’d like to pay. On the next page you’ll pay securely with Stripe.') }}</p>

        <form method="POST" action="{{ route('checkout.store', $template) }}" class="mt-8 space-y-8">
            @csrf
            <fieldset>
                <legend class="form-label">{{ __('Plan') }}</legend>
                <div class="mt-3 grid gap-4 sm:grid-cols-2">
                    @foreach ($plans as $plan)
                        <label class="card flex cursor-pointer gap-3 p-5 has-[:checked]:ring-2 has-[:checked]:ring-brand-700">
                            <input type="radio" name="plan" value="{{ $plan->value }}" class="mt-1 size-4 accent-brand-700" @checked(old('plan', $selectedPlan->value) === $plan->value) required>
                            <span>
                                <span class="block font-semibold text-ink">{{ $plan->label() }}</span>
                                <span class="block text-lg font-bold text-ink">{{ $plan->formattedPrice() }} <span class="text-sm font-normal text-slate-500">/ {{ $plan->intervalLabel() }} + {{ __('tax') }}</span></span>
                                <span class="mt-1 block text-sm text-slate-600">
                                    {{ $plan->hasMinimumCommitment()
                                        ? __(':months-month minimum', ['months' => config('gulraiz.commitment_months')])
                                        : __('Renews yearly. Save $100.') }}
                                </span>
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('plan')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
            </fieldset>

            <div class="card p-5">
                <h2 class="font-semibold text-ink">{{ __('Included with your subscription') }}</h2>
                <p class="mt-1 text-sm text-slate-600">{{ __('Website build, hosting, SSL, backups, domain, business email, support and :count edits a month.', ['count' => config('gulraiz.monthly_edit_limit')]) }}</p>
            </div>

            <div>
                <label class="flex gap-3 text-sm text-slate-700">
                    <input type="checkbox" name="accept_terms" value="1" class="mt-0.5 size-4 shrink-0 accent-brand-700" @checked(old('accept_terms')) required>
                    <span>
                        {!! __('I agree to the :terms and :cancellation. I understand that the monthly plan has a :months-month minimum: if I cancel earlier, billing continues until :months months are up.', [
                            'terms' => '<a href="'.e(route('legal.terms')).'" target="_blank" class="link">'.e(__('Terms of service')).'</a>',
                            'cancellation' => '<a href="'.e(route('legal.cancellation')).'" target="_blank" class="link">'.e(__('Cancellation policy')).'</a>',
                            'months' => (int) config('gulraiz.commitment_months'),
                        ]) !!}
                    </span>
                </label>
                @error('accept_terms')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <button type="submit" class="btn-primary px-6 py-3 text-base">{{ __('Continue to secure payment') }}</button>
                <p class="text-sm text-slate-500">{{ __('Tax is calculated from your billing address.') }}</p>
            </div>
        </form>
    </section>
</x-layouts.app>
