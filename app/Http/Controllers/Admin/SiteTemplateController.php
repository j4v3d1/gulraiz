<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SiteTemplateRequest;
use App\Models\SiteTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SiteTemplateController extends Controller
{
    public function index(): View
    {
        return view('admin.site-templates.index', [
            'templates' => SiteTemplate::query()->withCount('orders')->ordered()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.site-templates.form', ['template' => new SiteTemplate(['is_active' => true])]);
    }

    public function store(SiteTemplateRequest $request): RedirectResponse
    {
        SiteTemplate::create($this->attributes($request));

        return to_route('admin.site-templates.index')->with('status', __('Template created.'));
    }

    public function edit(SiteTemplate $siteTemplate): View
    {
        return view('admin.site-templates.form', ['template' => $siteTemplate]);
    }

    public function update(SiteTemplateRequest $request, SiteTemplate $siteTemplate): RedirectResponse
    {
        $attributes = $this->attributes($request);

        if (isset($attributes['thumbnail_path']) && $siteTemplate->thumbnail_path) {
            Storage::disk('public')->delete($siteTemplate->thumbnail_path);
        }

        $siteTemplate->update($attributes);

        return to_route('admin.site-templates.index')->with('status', __('Template updated.'));
    }

    public function destroy(SiteTemplate $siteTemplate): RedirectResponse
    {
        if ($siteTemplate->thumbnail_path) {
            Storage::disk('public')->delete($siteTemplate->thumbnail_path);
        }

        $siteTemplate->delete();

        return to_route('admin.site-templates.index')->with('status', __('Template deleted.'));
    }

    /**
     * @return array<string, mixed>
     */
    private function attributes(SiteTemplateRequest $request): array
    {
        $attributes = [
            ...$request->safe()->except(['thumbnail', 'features']),
            'features' => str($request->validated('features') ?? '')->explode("\n")->map(fn (string $line): string => trim($line))->filter()->values()->all(),
            'is_active' => $request->boolean('is_active'),
            'sort_order' => (int) $request->validated('sort_order'),
        ];

        if ($request->hasFile('thumbnail')) {
            $attributes['thumbnail_path'] = $request->file('thumbnail')->store('templates', 'public');
        }

        return $attributes;
    }
}
