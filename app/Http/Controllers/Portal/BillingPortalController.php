<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BillingPortalController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        if (! $request->user()->hasStripeId()) {
            return back()->with('error', __('You don’t have any billing history yet.'));
        }

        return $request->user()->redirectToBillingPortal(route('portal.dashboard'));
    }
}
