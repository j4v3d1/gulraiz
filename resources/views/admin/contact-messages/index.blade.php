<x-layouts.admin :title="__('Messages')">
    <div class="card divide-y divide-slate-100 overflow-hidden">
        @forelse ($contactMessages as $contactMessage)
            <a href="{{ route('admin.contact-messages.show', $contactMessage) }}" class="flex items-start justify-between gap-4 px-5 py-4 hover:bg-slate-50">
                <div class="min-w-0">
                    <p @class(['text-sm text-ink', 'font-semibold' => ! $contactMessage->read_at])>
                        @unless ($contactMessage->read_at)<span class="mr-1 inline-block size-2 rounded-full bg-brand-600" aria-label="{{ __('Unread') }}"></span>@endunless
                        {{ $contactMessage->name }} <span class="font-normal text-slate-500">&lt;{{ $contactMessage->email }}&gt;</span>
                    </p>
                    <p class="truncate text-sm text-slate-500">{{ $contactMessage->message }}</p>
                </div>
                <span class="shrink-0 text-xs text-slate-500">{{ $contactMessage->created_at->diffForHumans() }}</span>
            </a>
        @empty
            <p class="px-5 py-8 text-center text-sm text-slate-500">{{ __('No messages yet.') }}</p>
        @endforelse
    </div>
    {{ $contactMessages->links() }}
</x-layouts.admin>
