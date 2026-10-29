<x-auth-card :title="__('Log in')">
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf
        <x-field name="email" type="email" :label="__('Email')" required autofocus autocomplete="username" />
        <x-field name="password" type="password" :label="__('Password')" required autocomplete="current-password" />
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="size-4 accent-brand-700"> {{ __('Remember me') }}
            </label>
            <a href="{{ route('password.request') }}" class="link text-sm">{{ __('Forgot password?') }}</a>
        </div>
        <button type="submit" class="btn-primary w-full">{{ __('Log in') }}</button>
    </form>
    <x-slot:footer>
        <p class="mt-6 text-center text-sm text-slate-600">{{ __('New here?') }} <a href="{{ route('register') }}" class="link">{{ __('Create an account') }}</a></p>
    </x-slot:footer>
</x-auth-card>
