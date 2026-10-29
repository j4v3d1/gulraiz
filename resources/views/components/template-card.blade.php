@props(['template'])

<article class="group card flex flex-col overflow-hidden transition hover:-translate-y-0.5 hover:shadow-md">
    <a href="{{ route('templates.show', $template) }}" class="block aspect-[16/10] overflow-hidden bg-gradient-to-br from-brand-50 to-slate-100">
        @if ($template->thumbnailUrl())
            <img src="{{ $template->thumbnailUrl() }}" alt="{{ __('Preview of the :name template', ['name' => $template->name]) }}" loading="lazy" class="size-full object-cover transition group-hover:scale-[1.02]">
        @else
            <div class="flex size-full items-center justify-center text-sm font-medium text-brand-700/70">{{ $template->name }}</div>
        @endif
    </a>
    <div class="flex flex-1 flex-col gap-2 p-5">
        <p class="text-xs font-semibold uppercase tracking-wide text-accent-600">{{ $template->industry }}</p>
        <h3 class="text-lg font-semibold text-ink">
            <a href="{{ route('templates.show', $template) }}" class="hover:text-brand-700">{{ $template->name }}</a>
        </h3>
        <p class="flex-1 text-sm text-slate-600">{{ $template->summary }}</p>
        <div class="mt-3 flex gap-2">
            <a href="{{ route('templates.show', $template) }}" class="btn-primary flex-1">{{ __('View details') }}</a>
            @if ($template->demo_url)
                <a href="{{ route('templates.preview', $template) }}" class="btn-secondary">{{ __('Live demo') }}</a>
            @endif
        </div>
    </div>
</article>
