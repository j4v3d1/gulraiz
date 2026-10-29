<x-layouts.app :title="__('Website designs')" :description="__('Browse ready-made website designs for restaurants, salons, trades and more. Every design is customised for your business.')">
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <h1 class="text-4xl font-bold tracking-tight text-ink">{{ __('Choose your design') }}</h1>
        <p class="mt-3 max-w-2xl text-lg text-slate-600">{{ __('Every design is the same price. I customise it with your logo, colours, photos and text.') }}</p>

        @if ($industries->count() > 1)
            <nav class="mt-8 flex flex-wrap gap-2" aria-label="{{ __('Filter by industry') }}">
                <a href="{{ route('templates.index') }}" @class([
                    'rounded-full px-4 py-1.5 text-sm font-medium ring-1 ring-inset',
                    'bg-brand-700 text-white ring-brand-700' => $selectedIndustry === '',
                    'bg-white text-slate-700 ring-slate-300 hover:bg-slate-50' => $selectedIndustry !== '',
                ])>{{ __('All') }}</a>
                @foreach ($industries as $industry)
                    <a href="{{ route('templates.index', ['industry' => $industry]) }}" @class([
                        'rounded-full px-4 py-1.5 text-sm font-medium ring-1 ring-inset',
                        'bg-brand-700 text-white ring-brand-700' => $selectedIndustry === $industry,
                        'bg-white text-slate-700 ring-slate-300 hover:bg-slate-50' => $selectedIndustry !== $industry,
                    ]) @if ($selectedIndustry === $industry) aria-current="true" @endif>{{ $industry }}</a>
                @endforeach
            </nav>
        @endif

        @if ($templates->isEmpty())
            <p class="mt-12 text-slate-500">{{ __('No designs found. Check back soon, or contact me about a custom site.') }}</p>
        @else
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($templates as $template)
                    <x-template-card :template="$template" />
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.app>
