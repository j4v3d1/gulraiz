<x-layouts.admin :title="__('Message from :name', ['name' => $contactMessage->name])">
    <div class="card space-y-4 p-6">
        <dl class="grid gap-4 text-sm sm:grid-cols-3">
            <div><dt class="text-slate-500">{{ __('Email') }}</dt><dd><a href="mailto:{{ $contactMessage->email }}" class="link">{{ $contactMessage->email }}</a></dd></div>
            <div><dt class="text-slate-500">{{ __('Phone') }}</dt><dd class="text-ink">{{ $contactMessage->phone ?: '—' }}</dd></div>
            <div><dt class="text-slate-500">{{ __('Received') }}</dt><dd class="text-ink">{{ $contactMessage->created_at->toDayDateTimeString() }}</dd></div>
        </dl>
        <p class="border-t border-slate-100 pt-4 whitespace-pre-line text-ink">{{ $contactMessage->message }}</p>
        <div class="flex gap-3 border-t border-slate-100 pt-4">
            <a href="mailto:{{ $contactMessage->email }}" class="btn-primary">{{ __('Reply by email') }}</a>
            <form method="POST" action="{{ route('admin.contact-messages.destroy', $contactMessage) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-secondary">{{ __('Delete') }}</button>
            </form>
        </div>
    </div>
</x-layouts.admin>
