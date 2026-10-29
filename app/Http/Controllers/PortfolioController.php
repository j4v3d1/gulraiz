<?php

namespace App\Http\Controllers;

use App\Models\PortfolioItem;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function index(): View
    {
        return view('pages.portfolio', [
            'portfolioItems' => PortfolioItem::query()->published()->get(),
        ]);
    }
}
