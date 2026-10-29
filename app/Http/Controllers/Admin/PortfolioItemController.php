<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PortfolioItemRequest;
use App\Models\PortfolioItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PortfolioItemController extends Controller
{
    public function index(): View
    {
        return view('admin.portfolio-items.index', [
            'portfolioItems' => PortfolioItem::query()->orderBy('sort_order')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.portfolio-items.form', ['portfolioItem' => new PortfolioItem(['is_published' => true])]);
    }

    public function store(PortfolioItemRequest $request): RedirectResponse
    {
        PortfolioItem::create($this->attributes($request));

        return to_route('admin.portfolio-items.index')->with('status', __('Portfolio item created.'));
    }

    public function edit(PortfolioItem $portfolioItem): View
    {
        return view('admin.portfolio-items.form', ['portfolioItem' => $portfolioItem]);
    }

    public function update(PortfolioItemRequest $request, PortfolioItem $portfolioItem): RedirectResponse
    {
        $attributes = $this->attributes($request);

        if (isset($attributes['image_path']) && $portfolioItem->image_path) {
            Storage::disk('public')->delete($portfolioItem->image_path);
        }

        $portfolioItem->update($attributes);

        return to_route('admin.portfolio-items.index')->with('status', __('Portfolio item updated.'));
    }

    public function destroy(PortfolioItem $portfolioItem): RedirectResponse
    {
        if ($portfolioItem->image_path) {
            Storage::disk('public')->delete($portfolioItem->image_path);
        }

        $portfolioItem->delete();

        return to_route('admin.portfolio-items.index')->with('status', __('Portfolio item deleted.'));
    }

    /**
     * @return array<string, mixed>
     */
    private function attributes(PortfolioItemRequest $request): array
    {
        $attributes = [
            ...$request->safe()->except('image'),
            'is_published' => $request->boolean('is_published'),
            'sort_order' => (int) $request->validated('sort_order'),
        ];

        if ($request->hasFile('image')) {
            $attributes['image_path'] = $request->file('image')->store('portfolio', 'public');
        }

        return $attributes;
    }
}
