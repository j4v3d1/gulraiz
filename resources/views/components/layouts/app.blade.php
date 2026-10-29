@props(['title' => null, 'description' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' · Gulraiz' : __('Gulraiz · Websites for small businesses, built and hosted for you') }}</title>
    <meta name="description" content="{{ $description ?? __('Pick a design, and Mohammed Gulraiz builds, hosts and looks after your business website. $50/month or $500/year, everything included.') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Gulraiz">
    <meta property="og:title" content="{{ $title ?? 'Gulraiz' }}">
    <meta property="og:description" content="{{ $description ?? __('Websites for small businesses, built and hosted for you.') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{ $head ?? '' }}
</head>
<body class="flex min-h-full flex-col bg-slate-50 font-sans text-slate-700 antialiased">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:shadow">{{ __('Skip to content') }}</a>

    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/90 backdrop-blur">
        <nav class="mx-auto flex max-w-6xl items-center justify-between gap-6 px-4 py-3 sm:px-6" aria-label="{{ __('Main') }}">
            <a href="{{ route('home') }}" aria-label="{{ __('Gulraiz home') }}"><x-logo /></a>

            @php
                $links = [
                    'templates.index' => __('Templates'),
                    'pricing' => __('Pricing'),
                    'portfolio' => __('Portfolio'),
                    'about' => __('About'),
                    'contact.create' => __('Contact'),
                ];
            @endphp

            <ul class="hidden items-center gap-1 md:flex">
                @foreach ($links as $route => $label)
                    <li>
                        <a href="{{ route($route) }}" @class([
                            'rounded-lg px-3 py-2 text-sm font-medium transition hover:bg-slate-100 hover:text-ink',
                            'text-brand-700' => request()->routeIs($route, str($route)->before('.').'.*'),
                            'text-slate-600' => ! request()->routeIs($route, str($route)->before('.').'.*'),
                        ]) @if (request()->routeIs($route)) aria-current="page" @endif>{{ $label }}</a>
                    </li>
                @endforeach
            </ul>

            <div class="hidden items-center gap-2 md:flex">
                @auth
                    @can('admin')
                        <a href="{{ route('admin.dashboard') }}" class="btn-secondary">{{ __('Admin') }}</a>
                    @endcan
                    <a href="{{ route('portal.dashboard') }}" class="btn-primary">{{ __('My account') }}</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:text-ink">{{ __('Log in') }}</a>
                    <a href="{{ route('templates.index') }}" class="btn-primary">{{ __('Browse designs') }}</a>
                @endauth
            </div>

            <details class="relative md:hidden">
                <summary class="flex cursor-pointer list-none items-center rounded-lg p-2 text-ink hover:bg-slate-100" aria-label="{{ __('Open menu') }}">
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                </summary>
                <div class="absolute right-0 mt-2 w-64 rounded-xl bg-white p-2 shadow-lg ring-1 ring-slate-200">
                    @foreach ($links as $route => $label)
                        <a href="{{ route($route) }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">{{ $label }}</a>
                    @endforeach
                    <div class="my-2 border-t border-slate-100"></div>
                    @auth
                        <a href="{{ route('portal.dashboard') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-brand-700 hover:bg-slate-100">{{ __('My account') }}</a>
                    @else
                        <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-brand-700 hover:bg-slate-100">{{ __('Log in') }}</a>
                    @endauth
                </div>
            </details>
        </nav>
    </header>

    <main id="main" class="flex-1">
        @if (session('status') || session('error'))
            <div class="mx-auto max-w-6xl px-4 pt-6 sm:px-6"><x-flash /></div>
        @endif
        {{ $slot }}
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto grid max-w-6xl gap-8 px-4 py-12 sm:px-6 md:grid-cols-4">
            <div class="md:col-span-2">
                <x-logo />
                <p class="mt-3 max-w-sm text-sm text-slate-600">{{ __('Websites for small businesses, designed, built and hosted by Mohammed Gulraiz in Ontario, Canada.') }}</p>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-ink">{{ __('Explore') }}</h2>
                <ul class="mt-3 space-y-2 text-sm">
                    <li><a href="{{ route('templates.index') }}" class="hover:text-ink">{{ __('Templates') }}</a></li>
                    <li><a href="{{ route('pricing') }}" class="hover:text-ink">{{ __('Pricing') }}</a></li>
                    <li><a href="{{ route('portfolio') }}" class="hover:text-ink">{{ __('Portfolio') }}</a></li>
                    <li><a href="{{ route('contact.create') }}" class="hover:text-ink">{{ __('Contact') }}</a></li>
                </ul>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-ink">{{ __('The fine print') }}</h2>
                <ul class="mt-3 space-y-2 text-sm">
                    <li><a href="{{ route('legal.terms') }}" class="hover:text-ink">{{ __('Terms of service') }}</a></li>
                    <li><a href="{{ route('legal.privacy') }}" class="hover:text-ink">{{ __('Privacy policy') }}</a></li>
                    <li><a href="{{ route('legal.cancellation') }}" class="hover:text-ink">{{ __('Cancellation policy') }}</a></li>
                </ul>
            </div>
        </div>
        <p class="border-t border-slate-100 py-6 text-center text-xs text-slate-500">&copy; {{ now()->year }} Gulraiz. {{ __('All prices in Canadian dollars, plus applicable tax.') }}</p>
    </footer>
</body>
</html>
