<x-auth-card :title="__('Choose a new password')">
    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <x-field name="email" type="email" :label="__('Email')" :value="$request->email" required autocomplete="username" />
        <x-field name="password" type="password" :label="__('New password')" required autofocus autocomplete="new-password" />
        <x-field name="password_confirmation" type="password" :label="__('Confirm password')" required autocomplete="new-password" />
        <button type="submit" class="btn-primary w-full">{{ __('Reset password') }}</button>
    </form>
</x-auth-card>
