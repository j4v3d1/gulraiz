@php
    $included = [
        [__('Your website, built for you'), __('Pick a design and send your details. I build the whole site, so you don’t touch any code.')],
        [__('Hosting, SSL and backups'), __('Fast, secure hosting with HTTPS and regular backups. Nothing extra to set up or pay for.')],
        [__('Your own domain'), __('I register and renew your .ca or .com domain for you.')],
        [__('Business email'), __('A professional address like info@yourbusiness.ca.')],
        [__(':count edits every month', ['count' => config('gulraiz.monthly_edit_limit')]), __('Need new prices, photos or hours? Send a request from your account and I’ll handle it.')],
        [__('Real support'), __('You deal with me directly, not a call centre. Just send an email.')],
    ];
@endphp

<ul role="list" class="grid gap-x-8 gap-y-6 sm:grid-cols-2 lg:grid-cols-3">
    @foreach ($included as [$heading, $text])
        <li class="flex gap-3">
            <svg class="mt-0.5 size-5 shrink-0 text-accent-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" /></svg>
            <div>
                <p class="font-semibold text-ink">{{ $heading }}</p>
                <p class="mt-1 text-sm text-slate-600">{{ $text }}</p>
            </div>
        </li>
    @endforeach
</ul>
