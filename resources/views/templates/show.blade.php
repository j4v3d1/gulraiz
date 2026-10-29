<x-layouts.app :title="$template->name" :description="$template->summary">
    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
        <a href="{{ route('templates.index') }}" class="link text-sm">&larr; {{ __('All designs') }}</a>

        <div class="mt-6 grid gap-10 lg:grid-cols-5">
            <div class="lg:col-span-3">
                <div class="card overflow-hidden">
                    <div class="aspect-[16/10] bg-gradient-to-br from-brand-50 to-slate-100">
                        @if ($template->thumbnailUrl())
                            <img src="{{ $template->thumbnailUrl() }}" alt="{{ __('Preview of the :name template', ['name' => $template->name]) }}" class="size-full object-cover">
                        @else
                            <div class="flex size-full items-center justify-center text-lg font-medium text-brand-700/70">{{ $template->name }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <p class="text-sm font-semibold uppercase tracking-wide text-accent-600">{{ $template->industry }}</p>
                <h1 class="mt-2 text-4xl font-bold tracking-tight text-ink">{{ $template->name }}</h1>
                <p class="mt-4 text-lg text-slate-600">{{ $template->summary }}</p>

                <p class="mt-6 flex items-baseline gap-1">
                    <span class="text-3xl font-bold text-ink">{{ \App\Enums\Plan::Monthly->formattedPrice() }}</span>
                    <span class="text-slate-500">/ {{ __('month') }} + {{ __('tax') }}</span>
                    <span class="ml-2 text-sm text-slate-500">{{ __('or :price/year', ['price' => \App\Enums\Plan::Yearly->formattedPrice()]) }}</span>
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('checkout.create', $template) }}" class="btn-primary px-6 py-3 text-base">{{ __('Get this website') }}</a>
                    @if ($template->demo_url)
                        <a href="{{ route('templates.preview', $template) }}" class="btn-secondary px-6 py-3 text-base">{{ __('Try the live demo') }}</a>
                    @endif
                </div>

                @if (! empty($template->features))
                    <h2 class="mt-10 font-semibold text-ink">{{ __('Features') }}</h2>
                    <ul role="list" class="mt-3 space-y-2">
                        @foreach ($template->features as $feature)
                            <li class="flex gap-2 text-slate-600">
                                <svg class="mt-0.5 size-5 shrink-0 text-accent-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" /></svg>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="mt-16 max-w-3xl">
            <h2 class="text-2xl font-bold tracking-tight text-ink">{{ __('About this design') }}</h2>
            <div class="mt-4 space-y-4 leading-relaxed text-slate-600">
                @foreach (preg_split('/\R{2,}/', trim($template->description)) as $paragraph)
                    <p>{!! nl2br(e($paragraph)) !!}</p>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="mx-auto max-w-4xl px-4 sm:px-6">
            <h2 class="text-center text-2xl font-bold tracking-tight text-ink">{{ __('Choose your plan') }}</h2>
            <div class="mt-8">@include('partials.plans', ['template' => $template])</div>
        </div>
    </section>
</x-layouts.app>
