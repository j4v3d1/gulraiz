<x-layouts.admin :title="__('Edit requests')">
    <nav class="flex flex-wrap gap-2" aria-label="{{ __('Filter by status') }}">
        <a href="{{ route('admin.edit-requests.index') }}" @class(['rounded-full px-3 py-1 text-sm font-medium ring-1 ring-inset', 'bg-brand-700 text-white ring-brand-700' => ! $selectedStatus, 'bg-white ring-slate-300' => $selectedStatus])>{{ __('Not done') }}</a>
        @foreach (\App\Enums\EditRequestStatus::cases() as $status)
            <a href="{{ route('admin.edit-requests.index', ['status' => $status->value]) }}" @class(['rounded-full px-3 py-1 text-sm font-medium ring-1 ring-inset', 'bg-brand-700 text-white ring-brand-700' => $selectedStatus === $status, 'bg-white ring-slate-300' => $selectedStatus !== $status])>{{ $status->label() }}</a>
        @endforeach
    </nav>

    @forelse ($editRequests as $editRequest)
        <div class="card p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <a href="{{ route('admin.orders.show', $editRequest->order) }}" class="font-semibold text-ink hover:underline">{{ $editRequest->order->user->name }} &middot; {{ __('Order #:id', ['id' => $editRequest->order_id]) }}</a>
                    <p class="text-xs text-slate-500">{{ $editRequest->created_at->toDayDateTimeString() }}</p>
                </div>
                <x-badge :status="$editRequest->status" />
            </div>
            <p class="mt-3 text-sm whitespace-pre-line text-ink">{{ $editRequest->description }}</p>
            @if (! empty($editRequest->attachment_paths))
                <ul class="mt-2 flex flex-wrap gap-3 text-sm">
                    @foreach ($editRequest->attachment_paths as $path)
                        <li><a href="{{ route('admin.orders.file', [$editRequest->order_id, 'path' => $path]) }}" class="link">{{ __('Attachment :n', ['n' => $loop->iteration]) }}</a></li>
                    @endforeach
                </ul>
            @endif
            <form method="POST" action="{{ route('admin.edit-requests.update', $editRequest) }}" class="mt-4 flex flex-wrap items-end gap-3 border-t border-slate-100 pt-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="status-{{ $editRequest->id }}" class="form-label">{{ __('Status') }}</label>
                    <select id="status-{{ $editRequest->id }}" name="status" class="form-input mt-1">
                        @foreach (\App\Enums\EditRequestStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected($editRequest->status === $status)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-48 flex-1">
                    <label for="notes-{{ $editRequest->id }}" class="form-label">{{ __('Notes') }}</label>
                    <input id="notes-{{ $editRequest->id }}" name="admin_notes" value="{{ $editRequest->admin_notes }}" class="form-input mt-1">
                </div>
                <button type="submit" class="btn-primary">{{ __('Save') }}</button>
            </form>
        </div>
    @empty
        <p class="card p-8 text-center text-sm text-slate-500">{{ __('Nothing here. All caught up.') }}</p>
    @endforelse

    {{ $editRequests->links() }}
</x-layouts.admin>
