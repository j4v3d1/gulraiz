<x-layouts.app>
    <x-slot:head>
        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'ProfessionalService',
                'name' => 'Gulraiz',
                'url' => url('/'),
                'logo' => asset('images/logo-mark.svg'),
                'description' => 'Websites for small businesses, built and hosted for you.',
                'founder' => ['@type' => 'Person', 'name' => 'Mohammed Gulraiz'],
                'areaServed' => 'CA',
                'address' => ['@type' => 'PostalAddress', 'addressRegion' => 'ON', 'addressCountry' => 'CA'],
                'email' => config('gulraiz.admin_email'),
            ], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}
        </script>
    </x-slot:head>

    <section class="relative overflow-hidden bg-white">
        <div class="absolute inset-x-0 top-0 -z-0 h-96 bg-gradient-to-b from-brand-50 to-white" aria-hidden="true"></div>
        <div class="relative mx-auto grid max-w-6xl items-center gap-12 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:py-24">
            <div>
                <p class="inline-flex rounded-full bg-white px-3 py-1 text-sm font-medium text-brand-700 ring-1 ring-brand-100">{{ __('For small businesses in Canada') }}</p>
                <h1 class="mt-5 text-4xl font-bold tracking-tight text-ink sm:text-5xl">{{ __('A professional website for your business. Built and looked after for you.') }}</h1>
                <p class="mt-5 text-lg text-slate-600">{{ __('Pick a design you like. I build it with your logo, photos and text, then host and maintain it. No tech skills needed.') }}</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('templates.index') }}" class="btn-primary px-6 py-3 text-base">{{ __('Browse designs') }}</a>
                    <a href="{{ route('pricing') }}" class="btn-secondary px-6 py-3 text-base">{{ __('See pricing') }}</a>
                </div>
                <p class="mt-5 text-sm text-slate-500">{{ __('From $50/month + tax. Hosting, domain, email and edits included.') }}</p>
            </div>
            <div class="card p-6">
                <ol class="space-y-6">
                    @foreach ([
                        [__('Choose a design'), __('Browse ready-made designs for your industry and try the live demos.')],
                        [__('Subscribe and tell me about your business'), __('Pay securely, then fill in a short form with your details, logo and photos.')],
                        [__('Your website goes live'), __('I build it, you review it, and we launch. After that I keep it running.')],
                    ] as $index => [$heading, $text])
                        <li class="flex gap-4">
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-brand-700 text-sm font-bold text-white">{{ $index + 1 }}</span>
                            <div>
                                <p class="font-semibold text-ink">{{ $heading }}</p>
                                <p class="mt-1 text-sm text-slate-600">{{ $text }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </section>

    @if ($featuredTemplates->isNotEmpty())
        <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight text-ink">{{ __('Popular designs') }}</h2>
                    <p class="mt-2 text-slate-600">{{ __('Every design is the same price and gets customised for your business.') }}</p>
                </div>
                <a href="{{ route('templates.index') }}" class="link">{{ __('See all designs') }} &rarr;</a>
            </div>
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($featuredTemplates as $template)
                    <x-template-card :template="$template" />
                @endforeach
            </div>
        </section>
    @endif

    <section class="bg-white py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <h2 class="text-3xl font-bold tracking-tight text-ink">{{ __('Everything included') }}</h2>
            <p class="mt-2 text-slate-600">{{ __('One simple price. No surprise bills for hosting or renewals.') }}</p>
            <div class="mt-10">@include('partials.included')</div>
        </div>
    </section>

    @if ($portfolioItems->isNotEmpty())
        <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <h2 class="text-3xl font-bold tracking-tight text-ink">{{ __('Recent work') }}</h2>
                <a href="{{ route('portfolio') }}" class="link">{{ __('View portfolio') }} &rarr;</a>
            </div>
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($portfolioItems as $item)
                    @include('partials.portfolio-card', ['item' => $item])
                @endforeach
            </div>
        </section>
    @endif

    <section class="mx-auto max-w-6xl px-4 pb-20 sm:px-6">
        <div class="rounded-3xl bg-brand-700 px-6 py-12 text-center sm:px-12">
            <h2 class="text-3xl font-bold tracking-tight text-white">{{ __('Ready for a website that works for you?') }}</h2>
            <p class="mx-auto mt-3 max-w-xl text-brand-100">{{ __('Choose a design today. Most sites are live within two weeks of getting your details.') }}</p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('templates.index') }}" class="btn bg-white px-6 py-3 text-base text-brand-800 hover:bg-brand-50">{{ __('Browse designs') }}</a>
                <a href="{{ route('contact.create') }}" class="btn px-6 py-3 text-base text-white ring-1 ring-white/40 hover:bg-white/10">{{ __('Ask a question') }}</a>
            </div>
        </div>
    </section>
</x-layouts.app>
