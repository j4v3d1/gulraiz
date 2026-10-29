<x-layouts.app :title="__('Privacy policy')">
    <article class="mx-auto max-w-3xl px-4 py-16 sm:px-6">
        <p class="rounded-lg bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800 ring-1 ring-amber-200">{{ __('Draft: this page is a placeholder and must be reviewed before launch.') }}</p>
        <h1 class="mt-8 text-4xl font-bold tracking-tight text-ink">{{ __('Privacy policy') }}</h1>
        <div class="mt-8 space-y-4 leading-relaxed text-slate-600">
            <p>{{ __('This page will explain what personal information is collected (such as your name, email, business details and uploaded files), how it is used and stored, and your rights under Canadian privacy law (PIPEDA).') }}</p>
            <p>{{ __('Payments are processed by Stripe. Card details are never stored on this website.') }}</p>
        </div>
    </article>
</x-layouts.app>
