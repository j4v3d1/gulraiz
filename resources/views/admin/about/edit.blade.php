<x-layouts.admin :title="__('About page')">
    <form method="POST" action="{{ route('admin.about.update') }}" enctype="multipart/form-data" class="card space-y-5 p-6">
        @csrf
        @method('PUT')
        <x-field name="about_heading" :label="__('Heading')" :value="$heading" required placeholder="{{ __('Hi, I’m Mohammed Gulraiz.') }}" />
        <x-field name="about_body" type="textarea" rows="10" :label="__('Your story')" :value="$body" required :hint="__('Leave a blank line between paragraphs.')" />
        <div>
            <x-field name="about_photo" type="file" accept=".jpg,.jpeg,.png,.webp" :label="__('Photo')" />
            @if ($photoUrl)
                <img src="{{ $photoUrl }}" alt="" class="mt-3 w-32 rounded-lg ring-1 ring-slate-200">
            @endif
        </div>
        <div class="flex gap-3">
            <button type="submit" class="btn-primary">{{ __('Save') }}</button>
            <a href="{{ route('about') }}" target="_blank" class="btn-secondary">{{ __('View page') }}</a>
        </div>
    </form>
</x-layouts.admin>
