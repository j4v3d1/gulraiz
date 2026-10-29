<x-layouts.admin :title="__('Templates')">
    <x-slot:actions><a href="{{ route('admin.site-templates.create') }}" class="btn-primary">{{ __('Add template') }}</a></x-slot:actions>
    <div class="card overflow-hidden">
        @if ($templates->isEmpty())
            <p class="px-5 py-8 text-center text-sm text-slate-500">{{ __('No templates yet.') }}</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th scope="col" class="px-5 py-3">{{ __('Name') }}</th>
                            <th scope="col" class="px-5 py-3">{{ __('Industry') }}</th>
                            <th scope="col" class="px-5 py-3">{{ __('Orders') }}</th>
                            <th scope="col" class="px-5 py-3">{{ __('Visible') }}</th>
                            <th scope="col" class="px-5 py-3"><span class="sr-only">{{ __('Actions') }}</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach ($templates as $template)
                            <tr>
                                <td class="px-5 py-3 font-medium text-ink">{{ $template->name }}<br><span class="font-normal text-slate-500">/templates/{{ $template->slug }}</span></td>
                                <td class="px-5 py-3">{{ $template->industry }}</td>
                                <td class="px-5 py-3">{{ $template->orders_count }}</td>
                                <td class="px-5 py-3">{{ $template->is_active ? __('Yes') : __('Hidden') }}</td>
                                <td class="px-5 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.site-templates.edit', $template) }}" class="link">{{ __('Edit') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts.admin>
