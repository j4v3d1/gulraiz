@props(['wordmark' => true])

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
    <svg viewBox="0 0 64 64" class="size-9 shrink-0" aria-hidden="true">
        <rect width="64" height="64" rx="14" fill="#4338CA"/>
        <circle cx="30" cy="27" r="10" fill="none" stroke="#FFFFFF" stroke-width="6"/>
        <path d="M40 17 V39 a11 11 0 0 1 -11 11 h-7" fill="none" stroke="#FFFFFF" stroke-width="6" stroke-linecap="round"/>
        <circle cx="47" cy="47" r="3.5" fill="#10B981"/>
    </svg>
    @if ($wordmark)
        <span class="text-xl font-bold tracking-tight text-ink">gulraiz<span class="text-accent-500">.</span></span>
    @endif
    @unless ($wordmark)
        <span class="sr-only">Gulraiz</span>
    @endunless
</span>
