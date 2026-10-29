@if (session('status'))
    <div role="status" class="rounded-lg bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 ring-1 ring-emerald-200">
        {{ session('status') }}
    </div>
@endif

@if (session('error'))
    <div role="alert" class="rounded-lg bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800 ring-1 ring-rose-200">
        {{ session('error') }}
    </div>
@endif
