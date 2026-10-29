<x-auth-card :title="__('Create your account')" :intro="__('You’ll use this account to pay, send your details and request edits.')">
    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf
        <x-field name="name" :label="__('Your name')" required autofocus autocomplete="name" />
        <x-field name="email" type="email" :label="__('Email')" required autocomplete="username" />
        <x-field name="phone" type="tel" :label="__('Phone')" autocomplete="tel" />
        <x-field name="password" type="password" :label="__('Password')" required autocomplete="new-password" :hint="__('At least 8 characters.')" />
        <x-field name="password_confirmation" type="password" :label="__('Confirm password')" required autocomplete="new-password" />
        <button type="submit" class="btn-primary w-full">{{ __('Create account') }}</button>
    </form>
    <x-slot:footer>
        <p class="mt-6 text-center text-sm text-slate-600">{{ __('Already have an account?') }} <a href="{{ route('login') }}" class="link">{{ __('Log in') }}</a></p>
    </x-slot:footer>
</x-auth-card>
