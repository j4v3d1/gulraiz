<x-layouts.app :title="__('Portfolio')" :description="__('Websites Mohammed Gulraiz has built for small businesses.')">
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <h1 class="text-4xl font-bold tracking-tight text-ink">{{ __('Portfolio') }}</h1>
        <p class="mt-3 max-w-2xl text-lg text-slate-600">{{ __('A few of the websites I’ve built and look after for local businesses.') }}</p>

        @if ($portfolioItems->isEmpty())
            <p class="mt-12 text-slate-500">{{ __('New projects are coming soon.') }}</p>
        @else
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($portfolioItems as $item)
                    @include('partials.portfolio-card', ['item' => $item])
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.app>
