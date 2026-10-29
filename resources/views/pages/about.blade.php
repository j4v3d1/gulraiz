<x-layouts.app :title="__('About')" :description="__('Meet Mohammed Gulraiz, the web developer behind Gulraiz.')">
    <section class="mx-auto grid max-w-5xl items-start gap-12 px-4 py-16 sm:px-6 md:grid-cols-5">
        <div class="md:col-span-2">
            @if ($photoUrl)
                <img src="{{ $photoUrl }}" alt="{{ __('Photo of Mohammed Gulraiz') }}" class="aspect-square w-full rounded-3xl object-cover shadow-sm">
            @else
                <div class="flex aspect-square w-full items-center justify-center rounded-3xl bg-brand-50"><x-logo :wordmark="false" class="scale-[2.5]" /></div>
            @endif
        </div>
        <div class="md:col-span-3">
            <h1 class="text-4xl font-bold tracking-tight text-ink">{{ $heading }}</h1>
            <div class="mt-6 space-y-4 text-lg leading-relaxed text-slate-600">
                @foreach (preg_split('/\R{2,}/', trim($body)) as $paragraph)
                    <p>{!! nl2br(e($paragraph)) !!}</p>
                @endforeach
            </div>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('templates.index') }}" class="btn-primary">{{ __('Browse designs') }}</a>
                <a href="{{ route('contact.create') }}" class="btn-secondary">{{ __('Get in touch') }}</a>
            </div>
        </div>
    </section>
</x-layouts.app>
