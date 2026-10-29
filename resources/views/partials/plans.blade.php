@php($template = $template ?? null)

<div class="grid gap-6 md:grid-cols-2">
    @foreach (\App\Enums\Plan::cases() as $plan)
        <div @class([
            'card relative flex flex-col p-8',
            'ring-2 ring-brand-700' => $plan === \App\Enums\Plan::Yearly,
        ])>
            @if ($plan === \App\Enums\Plan::Yearly)
                <p class="absolute -top-3 left-8 rounded-full bg-accent-500 px-3 py-1 text-xs font-semibold text-white">{{ __('Save $100 a year') }}</p>
            @endif
            <h3 class="text-lg font-semibold text-ink">{{ $plan->label() }}</h3>
            <p class="mt-4 flex items-baseline gap-1">
                <span class="text-4xl font-bold tracking-tight text-ink">{{ $plan->formattedPrice() }}</span>
                <span class="text-sm text-slate-500">/ {{ $plan->intervalLabel() }} + {{ __('tax') }}</span>
            </p>
            <p class="mt-3 flex-1 text-sm text-slate-600">
                @if ($plan->hasMinimumCommitment())
                    {{ __('Pay as you go, with a :months-month minimum. That covers the cost of building your site.', ['months' => config('gulraiz.commitment_months')]) }}
                @else
                    {{ __('One payment a year. Renews automatically and you can cancel any time before renewal.') }}
                @endif
            </p>
            <a href="{{ $template ? route('checkout.create', [$template, 'plan' => $plan->value]) : route('templates.index') }}"
               @class(['mt-6', 'btn-primary' => $plan === \App\Enums\Plan::Yearly, 'btn-secondary' => $plan !== \App\Enums\Plan::Yearly])>
                {{ $template ? __('Choose :plan', ['plan' => strtolower($plan->label())]) : __('Pick a design') }}
            </a>
        </div>
    @endforeach
</div>
