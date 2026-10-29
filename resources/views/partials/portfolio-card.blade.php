<article class="card overflow-hidden">
    <div class="aspect-[16/10] bg-gradient-to-br from-slate-100 to-brand-50">
        @if ($item->imageUrl())
            <img src="{{ $item->imageUrl() }}" alt="{{ __('Screenshot of :title', ['title' => $item->title]) }}" loading="lazy" class="size-full object-cover">
        @endif
    </div>
    <div class="p-5">
        <h3 class="font-semibold text-ink">{{ $item->title }}</h3>
        @if ($item->client_name)
            <p class="text-sm text-slate-500">{{ $item->client_name }}</p>
        @endif
        @if ($item->summary)
            <p class="mt-2 text-sm text-slate-600">{{ $item->summary }}</p>
        @endif
        @if ($item->url)
            <a href="{{ $item->url }}" target="_blank" rel="noopener noreferrer" class="link mt-3 inline-block text-sm">{{ __('Visit site') }} &nearr;</a>
        @endif
    </div>
</article>
