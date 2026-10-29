<x-auth-card :title="__('Check your email')" :intro="__('We sent a link to :email. Click it to confirm your address and continue.', ['email' => auth()->user()->email])">
    <div class="space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-primary w-full">{{ __('Send the link again') }}</button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-secondary w-full">{{ __('Log out') }}</button>
        </form>
    </div>
</x-auth-card>
