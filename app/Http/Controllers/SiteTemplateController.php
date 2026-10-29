<?php

namespace App\Http\Controllers;

use App\Models\SiteTemplate;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteTemplateController extends Controller
{
    public function index(Request $request): View
    {
        $industry = $request->string('industry')->toString();

        return view('templates.index', [
            'templates' => SiteTemplate::query()
                ->active()
                ->when($industry !== '', fn ($query) => $query->where('industry', $industry))
                ->ordered()
                ->get(),
            'industries' => SiteTemplate::query()->active()->distinct()->orderBy('industry')->pluck('industry'),
            'selectedIndustry' => $industry,
        ]);
    }

    public function show(SiteTemplate $siteTemplate): View
    {
        abort_unless($siteTemplate->is_active, 404);

        return view('templates.show', ['template' => $siteTemplate]);
    }
}
