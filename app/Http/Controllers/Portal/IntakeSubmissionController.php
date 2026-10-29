<?php

namespace App\Http\Controllers\Portal;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\StoreIntakeSubmissionRequest;
use App\Models\Order;
use App\Notifications\IntakeSubmitted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class IntakeSubmissionController extends Controller
{
    public function create(Order $order): View
    {
        Gate::authorize('submitIntake', $order);

        return view('portal.intake.create', ['order' => $order]);
    }

    public function store(StoreIntakeSubmissionRequest $request, Order $order): RedirectResponse
    {
        $directory = "orders/{$order->id}/intake";

        $order->intakeSubmission()->create([
            ...$request->safe()->except(['logo', 'photos']),
            'logo_path' => $request->file('logo')?->store($directory, 'local'),
            'photo_paths' => collect($request->file('photos', []))
                ->map(fn (UploadedFile $photo): string => $photo->store($directory, 'local'))
                ->all(),
        ]);

        $order->update(['status' => OrderStatus::Building]);

        Notification::route('mail', config('gulraiz.admin_email'))->notify(new IntakeSubmitted($order));

        return to_route('portal.orders.show', $order)->with('status', __('Thanks! I have everything I need and will start building your website.'));
    }
}
