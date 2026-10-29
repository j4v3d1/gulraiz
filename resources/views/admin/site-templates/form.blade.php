<x-layouts.admin :title="$template->exists ? __('Edit :name', ['name' => $template->name]) : __('Add template')">
    <form method="POST" action="{{ $template->exists ? route('admin.site-templates.update', $template) : route('admin.site-templates.store') }}" enctype="multipart/form-data" class="card space-y-5 p-6">
        @csrf
        @if ($template->exists) @method('PUT') @endif
        <div class="grid gap-5 sm:grid-cols-2">
            <x-field name="name" :label="__('Name')" :value="$template->name" required />
            <x-field name="slug" :label="__('URL slug')" :value="$template->slug" required :hint="__('Letters, numbers and dashes, e.g. bistro-modern')" />
            <x-field name="industry" :label="__('Industry')" :value="$template->industry" required :hint="__('Used for the gallery filter, e.g. Restaurant')" />
            <x-field name="sort_order" type="number" min="0" :label="__('Sort order')" :value="$template->sort_order" :hint="__('Lower numbers show first.')" />
        </div>
        <x-field name="summary" :label="__('One-line summary')" :value="$template->summary" required />
        <x-field name="description" type="textarea" rows="6" :label="__('Description')" :value="$template->description" required />
        <x-field name="features" type="textarea" rows="5" :label="__('Features (one per line)')" :value="implode(PHP_EOL, $template->features ?? [])" />
        <x-field name="demo_url" type="url" :label="__('Live demo URL')" :value="$template->demo_url" :hint="__('e.g. https://demo.gulraiz.ca/bistro-modern. The demo site must allow being shown in a frame.')" />
        <div>
            <x-field name="thumbnail" type="file" accept=".jpg,.jpeg,.png,.webp" :label="__('Screenshot')" :hint="__('16:10 works best. JPG, PNG or WebP, up to 5 MB.')" />
            @if ($template->thumbnailUrl())
                <img src="{{ $template->thumbnailUrl() }}" alt="" class="mt-3 w-48 rounded-lg ring-1 ring-slate-200">
            @endif
        </div>
        <label class="flex items-center gap-2 text-sm font-medium text-ink">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="size-4 accent-brand-700" @checked(old('is_active', $template->is_active))>
            {{ __('Show on the website') }}
        </label>
        <div class="flex gap-3">
            <button type="submit" class="btn-primary">{{ __('Save') }}</button>
            <a href="{{ route('admin.site-templates.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
        </div>
    </form>
    @if ($template->exists)
        <form method="POST" action="{{ route('admin.site-templates.destroy', $template) }}" class="text-right">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-sm font-medium text-rose-600 hover:underline">{{ __('Delete this template') }}</button>
            <p class="text-xs text-slate-500">{{ __('Existing orders are kept. To just hide it, untick “Show on the website”.') }}</p>
        </form>
    @endif
</x-layouts.admin>
