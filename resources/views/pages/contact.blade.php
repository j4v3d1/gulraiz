<x-layouts.app :title="__('Contact')" :description="__('Questions about a website for your business? Send Mohammed Gulraiz a message.')">
    <section class="mx-auto grid max-w-5xl gap-12 px-4 py-16 sm:px-6 md:grid-cols-5">
        <div class="md:col-span-2">
            <h1 class="text-4xl font-bold tracking-tight text-ink">{{ __('Let’s talk') }}</h1>
            <p class="mt-4 text-lg text-slate-600">{{ __('Not sure which design fits, or have a question about how it works? Send a message and I’ll reply within one business day.') }}</p>
            <p class="mt-6 text-sm text-slate-600">{{ __('Or email') }} <a href="mailto:{{ config('gulraiz.admin_email') }}" class="link">{{ config('gulraiz.admin_email') }}</a></p>
        </div>
        <form method="POST" action="{{ route('contact.store') }}" class="card space-y-5 p-6 md:col-span-3">
            @csrf
            <x-field name="name" :label="__('Your name')" required autocomplete="name" />
            <x-field name="email" type="email" :label="__('Email')" required autocomplete="email" />
            <x-field name="phone" type="tel" :label="__('Phone')" autocomplete="tel" />
            <x-field name="message" type="textarea" :label="__('How can I help?')" required rows="5" />
            <div class="hidden" aria-hidden="true">
                <label for="company_website">{{ __('Leave this field empty') }}</label>
                <input id="company_website" type="text" name="company_website" tabindex="-1" autocomplete="off">
            </div>
            <button type="submit" class="btn-primary w-full sm:w-auto">{{ __('Send message') }}</button>
        </form>
    </section>
</x-layouts.app>
