<?php

namespace App\Http\Controllers;

use App\Actions\CreateCheckoutSession;
use App\Enums\OrderStatus;
use App\Enums\Plan;
use App\Http\Requests\StoreCheckoutRequest;
use App\Models\SiteTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function create(SiteTemplate $siteTemplate): View
    {
        abort_unless($siteTemplate->is_active, 404);

        return view('checkout.create', [
            'template' => $siteTemplate,
            'plans' => Plan::cases(),
            'selectedPlan' => Plan::tryFrom(request()->string('plan')->toString()) ?? Plan::Monthly,
        ]);
    }

    public function store(StoreCheckoutRequest $request, SiteTemplate $siteTemplate, CreateCheckoutSession $createCheckoutSession): RedirectResponse
    {
        abort_unless($siteTemplate->is_active, 404);

        $order = $request->user()->orders()->create([
            'site_template_id' => $siteTemplate->id,
            'plan' => $request->enum('plan', Plan::class),
            'status' => OrderStatus::PendingPayment,
            'commitment_accepted_at' => now(),
        ]);

        $order->forceFill(['subscription_type' => "order-{$order->id}"])->save();

        return redirect()->away($createCheckoutSession->handle($order), 303);
    }
}
