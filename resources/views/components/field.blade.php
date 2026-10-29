@props(['name', 'label', 'type' => 'text', 'value' => null, 'hint' => null, 'required' => false])

@php
    $id = $attributes->get('id', str_replace(['[', ']'], ['_', ''], $name));
    $errorKey = str_replace(['[]', '[', ']'], ['', '.', ''], $name);
@endphp

<div>
    <label for="{{ $id }}" class="form-label">
        {{ $label }}
        @unless ($required)
            <span class="font-normal text-slate-500">({{ __('optional') }})</span>
        @endunless
    </label>
    <div class="mt-1.5">
        @if ($type === 'textarea')
            <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $attributes->get('rows', 4) }}" @required($required)
                @if ($hint) aria-describedby="{{ $id }}_hint" @endif
                {{ $attributes->except(['id', 'rows'])->merge(['class' => 'form-input']) }}>{{ old($errorKey, $value) }}</textarea>
        @else
            <input id="{{ $id }}" type="{{ $type }}" name="{{ $name }}" @required($required)
                @if ($type !== 'password' && $type !== 'file') value="{{ old($errorKey, $value) }}" @endif
                @if ($hint) aria-describedby="{{ $id }}_hint" @endif
                {{ $attributes->except('id')->merge(['class' => $type === 'file' ? 'block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100' : 'form-input']) }}>
        @endif
    </div>
    @if ($hint)
        <p id="{{ $id }}_hint" class="mt-1.5 text-sm text-slate-500">{{ $hint }}</p>
    @endif
    @error($errorKey)
        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
    @enderror
    @error($errorKey.'.*')
        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
    @enderror
</div>
