<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderFileController extends Controller
{
    /**
     * Download a file a client uploaded for this order. Only paths recorded
     * on the order's intake form or edit requests can be fetched.
     */
    public function show(Request $request, Order $order): StreamedResponse
    {
        $path = $request->string('path')->toString();

        $order->load(['intakeSubmission', 'editRequests']);

        $knownPaths = collect([$order->intakeSubmission?->logo_path])
            ->merge($order->intakeSubmission?->photo_paths ?? [])
            ->merge($order->editRequests->pluck('attachment_paths')->flatten())
            ->filter();

        abort_unless($knownPaths->contains($path) && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path);
    }
}
