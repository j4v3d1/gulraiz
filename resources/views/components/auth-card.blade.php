@props(['title', 'intro' => null])

<x-layouts.app :title="$title">
    <section class="mx-auto max-w-md px-4 py-16 sm:px-6">
        <h1 class="text-center text-3xl font-bold tracking-tight text-ink">{{ $title }}</h1>
        @if ($intro)
            <p class="mt-3 text-center text-slate-600">{{ $intro }}</p>
        @endif
        <div class="card mt-8 p-6 sm:p-8">
            {{ $slot }}
        </div>
        {{ $footer ?? '' }}
    </section>
</x-layouts.app>
