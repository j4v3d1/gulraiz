<x-layouts.admin :title="$portfolioItem->exists ? __('Edit :title', ['title' => $portfolioItem->title]) : __('Add project')">
    <form method="POST" action="{{ $portfolioItem->exists ? route('admin.portfolio-items.update', $portfolioItem) : route('admin.portfolio-items.store') }}" enctype="multipart/form-data" class="card space-y-5 p-6">
        @csrf
        @if ($portfolioItem->exists) @method('PUT') @endif
        <div class="grid gap-5 sm:grid-cols-2">
            <x-field name="title" :label="__('Title')" :value="$portfolioItem->title" required />
            <x-field name="client_name" :label="__('Client')" :value="$portfolioItem->client_name" />
            <x-field name="url" type="url" :label="__('Website URL')" :value="$portfolioItem->url" />
            <x-field name="sort_order" type="number" min="0" :label="__('Sort order')" :value="$portfolioItem->sort_order" />
        </div>
        <x-field name="summary" type="textarea" :label="__('Summary')" :value="$portfolioItem->summary" />
        <div>
            <x-field name="image" type="file" accept=".jpg,.jpeg,.png,.webp" :label="__('Screenshot')" />
            @if ($portfolioItem->imageUrl())
                <img src="{{ $portfolioItem->imageUrl() }}" alt="" class="mt-3 w-48 rounded-lg ring-1 ring-slate-200">
            @endif
        </div>
        <label class="flex items-center gap-2 text-sm font-medium text-ink">
            <input type="hidden" name="is_published" value="0">
            <input type="checkbox" name="is_published" value="1" class="size-4 accent-brand-700" @checked(old('is_published', $portfolioItem->is_published))>
            {{ __('Show on the website') }}
        </label>
        <div class="flex gap-3">
            <button type="submit" class="btn-primary">{{ __('Save') }}</button>
            <a href="{{ route('admin.portfolio-items.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
        </div>
    </form>
    @if ($portfolioItem->exists)
        <form method="POST" action="{{ route('admin.portfolio-items.destroy', $portfolioItem) }}" class="text-right">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-sm font-medium text-rose-600 hover:underline">{{ __('Delete this project') }}</button>
        </form>
    @endif
</x-layouts.admin>
