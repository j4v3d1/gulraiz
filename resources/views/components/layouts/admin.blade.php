@props(['title'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $title }} · {{ __('Admin') }} · Gulraiz</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-slate-100 font-sans text-slate-700 antialiased">
    @php
        $nav = [
            'admin.dashboard' => [__('Dashboard'), 'admin.dashboard'],
            'admin.orders.index' => [__('Orders'), 'admin.orders.*'],
            'admin.edit-requests.index' => [__('Edit requests'), 'admin.edit-requests.*'],
            'admin.contact-messages.index' => [__('Messages'), 'admin.contact-messages.*'],
            'admin.site-templates.index' => [__('Templates'), 'admin.site-templates.*'],
            'admin.portfolio-items.index' => [__('Portfolio'), 'admin.portfolio-items.*'],
            'admin.about.edit' => [__('About page'), 'admin.about.*'],
        ];
    @endphp
    <div class="lg:flex">
        <aside class="border-b border-slate-200 bg-white lg:sticky lg:top-0 lg:h-screen lg:w-60 lg:shrink-0 lg:border-r lg:border-b-0">
            <div class="flex items-center justify-between px-4 py-4 lg:block">
                <a href="{{ route('admin.dashboard') }}"><x-logo /></a>
                <p class="hidden text-xs font-semibold uppercase tracking-wide text-slate-400 lg:mt-1 lg:block">{{ __('Admin') }}</p>
            </div>
            <nav class="flex gap-1 overflow-x-auto px-3 pb-3 lg:flex-col lg:overflow-visible" aria-label="{{ __('Admin') }}">
                @foreach ($nav as $route => [$label, $pattern])
                    <a href="{{ route($route) }}" @class([
                        'shrink-0 rounded-lg px-3 py-2 text-sm font-medium',
                        'bg-brand-50 text-brand-700' => request()->routeIs($pattern),
                        'text-slate-600 hover:bg-slate-100' => ! request()->routeIs($pattern),
                    ])>{{ $label }}</a>
                @endforeach
                <div class="hidden border-t border-slate-100 pt-2 lg:mt-4 lg:block">
                    <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2 text-sm text-slate-500 hover:bg-slate-100">{{ __('View site') }} &rarr;</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="w-full rounded-lg px-3 py-2 text-left text-sm text-slate-500 hover:bg-slate-100">{{ __('Log out') }}</button>
                    </form>
                </div>
            </nav>
        </aside>
        <main class="min-w-0 flex-1 px-4 py-8 sm:px-8">
            <div class="mx-auto max-w-5xl space-y-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <h1 class="text-2xl font-bold tracking-tight text-ink">{{ $title }}</h1>
                    {{ $actions ?? '' }}
                </div>
                <x-flash />
                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
