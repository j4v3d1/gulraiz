<x-auth-card :title="__('Reset your password')" :intro="__('Enter your email and we’ll send you a link to choose a new password.')">
    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <x-field name="email" type="email" :label="__('Email')" required autofocus autocomplete="username" />
        <button type="submit" class="btn-primary w-full">{{ __('Send reset link') }}</button>
    </form>
</x-auth-card>
