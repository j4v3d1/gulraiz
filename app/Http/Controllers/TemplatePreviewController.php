<?php

namespace App\Http\Controllers;

use App\Models\SiteTemplate;
use Illuminate\View\View;

class TemplatePreviewController extends Controller
{
    public function __invoke(SiteTemplate $siteTemplate): View
    {
        abort_unless($siteTemplate->is_active && $siteTemplate->demo_url, 404);

        return view('templates.preview', ['template' => $siteTemplate]);
    }
}
