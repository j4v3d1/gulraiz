<x-layouts.app :title="__('Tell me about your business')">
    <section class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
        <a href="{{ route('portal.orders.show', $order) }}" class="link text-sm">&larr; {{ __('Back to your order') }}</a>
        <h1 class="mt-4 text-3xl font-bold tracking-tight text-ink">{{ __('Tell me about your business') }}</h1>
        <p class="mt-2 text-slate-600">{{ __('This is what I’ll use to build your website. Don’t worry about perfect wording; I’ll tidy it up.') }}</p>

        <form method="POST" action="{{ route('portal.orders.intake.store', $order) }}" enctype="multipart/form-data" class="mt-8 space-y-8">
            @csrf
            <fieldset class="card space-y-5 p-6">
                <legend class="sr-only">{{ __('Business details') }}</legend>
                <h2 class="text-lg font-semibold text-ink">{{ __('Business details') }}</h2>
                <x-field name="business_name" :label="__('Business name')" required autocomplete="organization" />
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-field name="contact_email" type="email" :label="__('Email customers should use')" :value="auth()->user()->email" required />
                    <x-field name="contact_phone" type="tel" :label="__('Phone customers should call')" :value="auth()->user()->phone" />
                </div>
                <x-field name="desired_domain" :label="__('Web address you’d like')" placeholder="yourbusiness.ca" :hint="__('I’ll check it’s available and register it for you.')" />
            </fieldset>

            <fieldset class="card space-y-5 p-6">
                <legend class="sr-only">{{ __('Content') }}</legend>
                <h2 class="text-lg font-semibold text-ink">{{ __('What goes on the site') }}</h2>
                <x-field name="about_text" type="textarea" rows="5" :label="__('About your business')" required :hint="__('Who you are, what you do, how long you’ve been doing it, and what makes you different.')" />
                <x-field name="services_text" type="textarea" rows="5" :label="__('Services or products, with prices if you want them shown')" />
                <x-field name="colour_preferences" :label="__('Colour preferences')" placeholder="{{ __('e.g. navy and gold, or match my logo') }}" />
                <x-field name="extra_notes" type="textarea" :label="__('Anything else?')" :hint="__('Opening hours, service area, social media links, websites you like…')" />
            </fieldset>

            <fieldset class="card space-y-5 p-6">
                <legend class="sr-only">{{ __('Files') }}</legend>
                <h2 class="text-lg font-semibold text-ink">{{ __('Logo and photos') }}</h2>
                <x-field name="logo" type="file" :label="__('Logo')" accept=".jpg,.jpeg,.png,.webp" :hint="__('JPG, PNG or WebP, up to 5 MB. No logo? I can make a simple one.')" />
                <x-field name="photos[]" id="photos" type="file" :label="__('Photos')" multiple accept=".jpg,.jpeg,.png,.webp" :hint="__('Up to 10 photos of your work, shop or team. 8 MB each.')" />
            </fieldset>

            <button type="submit" class="btn-primary px-6 py-3 text-base">{{ __('Send my details') }}</button>
        </form>
    </section>
</x-layouts.app>
