@if ($orders->isEmpty())
    <p class="px-5 py-8 text-center text-sm text-slate-500">{{ __('No orders yet.') }}</p>
@else
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th scope="col" class="px-5 py-3">#</th>
                    <th scope="col" class="px-5 py-3">{{ __('Client') }}</th>
                    <th scope="col" class="px-5 py-3">{{ __('Template') }}</th>
                    <th scope="col" class="px-5 py-3">{{ __('Plan') }}</th>
                    <th scope="col" class="px-5 py-3">{{ __('Status') }}</th>
                    <th scope="col" class="px-5 py-3">{{ __('Paid') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @foreach ($orders as $order)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3"><a href="{{ route('admin.orders.show', $order) }}" class="link">{{ $order->id }}</a></td>
                        <td class="px-5 py-3"><a href="{{ route('admin.orders.show', $order) }}" class="font-medium text-ink hover:underline">{{ $order->user->name }}</a><br><span class="text-slate-500">{{ $order->user->email }}</span></td>
                        <td class="px-5 py-3">{{ $order->siteTemplate?->name ?? '—' }}</td>
                        <td class="px-5 py-3">{{ $order->plan->label() }}</td>
                        <td class="px-5 py-3"><x-badge :status="$order->status" /></td>
                        <td class="px-5 py-3 whitespace-nowrap text-slate-500">{{ $order->paid_at?->toFormattedDateString() ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
