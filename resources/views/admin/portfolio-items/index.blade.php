<x-layouts.admin :title="__('Portfolio')">
    <x-slot:actions><a href="{{ route('admin.portfolio-items.create') }}" class="btn-primary">{{ __('Add project') }}</a></x-slot:actions>
    <div class="card divide-y divide-slate-100 overflow-hidden">
        @forelse ($portfolioItems as $item)
            <div class="flex items-center justify-between gap-4 px-5 py-4">
                <div>
                    <p class="font-medium text-ink">{{ $item->title }}</p>
                    <p class="text-sm text-slate-500">{{ $item->client_name }} @unless ($item->is_published) &middot; {{ __('Hidden') }} @endunless</p>
                </div>
                <a href="{{ route('admin.portfolio-items.edit', $item) }}" class="link text-sm">{{ __('Edit') }}</a>
            </div>
        @empty
            <p class="px-5 py-8 text-center text-sm text-slate-500">{{ __('No projects yet.') }}</p>
        @endforelse
    </div>
</x-layouts.admin>
