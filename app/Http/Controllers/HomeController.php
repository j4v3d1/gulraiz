<?php

namespace App\Http\Controllers;

use App\Models\PortfolioItem;
use App\Models\SiteTemplate;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.home', [
            'featuredTemplates' => SiteTemplate::query()->active()->ordered()->limit(3)->get(),
            'portfolioItems' => PortfolioItem::query()->published()->limit(3)->get(),
        ]);
    }
}
