<x-layouts.app :title="__('Cancellation policy')">
    <article class="mx-auto max-w-3xl px-4 py-16 sm:px-6">
        <p class="rounded-lg bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800 ring-1 ring-amber-200">{{ __('Draft: this page is a placeholder and must be reviewed before launch.') }}</p>
        <h1 class="mt-8 text-4xl font-bold tracking-tight text-ink">{{ __('Cancellation policy') }}</h1>
        <div class="mt-8 space-y-4 leading-relaxed text-slate-600">
            <p>{{ __('Monthly plan: includes a :months-month minimum commitment. If you cancel within that time, your subscription stays active and billing continues until the minimum term ends. After that, you can cancel any time and it takes effect at the end of the current month.', ['months' => config('gulraiz.commitment_months')]) }}</p>
            <p>{{ __('Yearly plan: you can cancel any time, and it takes effect at the end of the year you have paid for.') }}</p>
            <p>{{ __('What happens to your website, domain and email after cancellation is still to be confirmed.') }}</p>
        </div>
    </article>
</x-layouts.app>
