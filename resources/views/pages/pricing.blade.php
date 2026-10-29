<x-layouts.app :title="__('Pricing')" :description="__('$50/month or $500/year plus tax. Website build, hosting, domain, business email and monthly edits included.')">
    <section class="mx-auto max-w-4xl px-4 py-16 text-center sm:px-6">
        <h1 class="text-4xl font-bold tracking-tight text-ink">{{ __('Simple pricing, everything included') }}</h1>
        <p class="mx-auto mt-4 max-w-2xl text-lg text-slate-600">{{ __('Every design costs the same. Your subscription covers building the site and keeping it running.') }}</p>
    </section>

    <section class="mx-auto max-w-4xl px-4 sm:px-6">
        @include('partials.plans')
        <p class="mt-4 text-center text-sm text-slate-500">{{ __('Prices in CAD. GST/HST is added at checkout based on your province.') }}</p>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <h2 class="text-2xl font-bold tracking-tight text-ink">{{ __('What you get') }}</h2>
        <div class="mt-8">@include('partials.included')</div>
    </section>

    <section class="bg-white py-16">
        <div class="mx-auto max-w-3xl px-4 sm:px-6">
            <h2 class="text-2xl font-bold tracking-tight text-ink">{{ __('Questions') }}</h2>
            <div class="mt-6 divide-y divide-slate-200">
                @foreach ([
                    [__('Why is there a 12-month minimum on the monthly plan?'), __('Building your website takes real work up front. Rather than charge a big setup fee, I spread it over your first year. After 12 months you can cancel any time.')],
                    [__('What happens if I cancel?'), __('Your site stays online until the end of the period you’ve paid for. On the monthly plan, cancelling in the first 12 months means billing continues until the 12 months are up.')],
                    [__('How long does it take?'), __('Most websites are ready for your review within two weeks of getting your details, logo and photos.')],
                    [__('Can I change things later?'), __(':count edits a month are included. Send them from your account, like updating hours, prices, photos or text.', ['count' => config('gulraiz.monthly_edit_limit')])],
                    [__('Do I need to know anything technical?'), __('No. You tell me about your business and I handle the domain, hosting, email and the website itself.')],
                    [__('How do I pay?'), __('By credit or debit card through Stripe. Your card details never touch this website.')],
                ] as [$question, $answer])
                    <details class="group py-4">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-semibold text-ink">
                            {{ $question }}
                            <span class="text-slate-400 transition group-open:rotate-45" aria-hidden="true">+</span>
                        </summary>
                        <p class="mt-3 text-slate-600">{{ $answer }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>
</x-layouts.app>
