<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ __(':name live demo', ['name' => $template->name]) }} · Gulraiz</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @fonts
    @vite(['resources/css/app.css'])
</head>
<body class="flex h-full flex-col bg-slate-900 font-sans antialiased">
    <header class="flex flex-wrap items-center justify-between gap-3 bg-white px-4 py-2.5 shadow">
        <div class="flex min-w-0 items-center gap-4">
            <a href="{{ route('templates.show', $template) }}" aria-label="{{ __('Back to :name', ['name' => $template->name]) }}"><x-logo :wordmark="false" /></a>
            <p class="truncate text-sm text-slate-600"><span class="font-semibold text-ink">{{ $template->name }}</span> &middot; {{ __('live demo') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ $template->demo_url }}" target="_blank" rel="noopener noreferrer" class="hidden text-sm font-medium text-slate-500 hover:text-ink sm:inline">{{ __('Open in new tab') }} &nearr;</a>
            <a href="{{ route('checkout.create', $template) }}" class="btn-primary">{{ __('Choose this design') }}</a>
        </div>
    </header>
    <iframe src="{{ $template->demo_url }}" title="{{ __(':name live demo', ['name' => $template->name]) }}" class="w-full flex-1 border-0 bg-white" loading="eager"></iframe>
</body>
</html>
