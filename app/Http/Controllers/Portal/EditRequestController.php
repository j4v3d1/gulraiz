<?php

namespace App\Http\Controllers\Portal;

use App\Enums\EditRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\StoreEditRequestRequest;
use App\Models\Order;
use App\Notifications\EditRequestSubmitted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;

class EditRequestController extends Controller
{
    public function store(StoreEditRequestRequest $request, Order $order): RedirectResponse
    {
        $editRequest = $order->editRequests()->create([
            'description' => $request->validated('description'),
            'status' => EditRequestStatus::Open,
            'attachment_paths' => collect($request->file('attachments', []))
                ->map(fn (UploadedFile $file): string => $file->store("orders/{$order->id}/edits", 'local'))
                ->all(),
        ]);

        Notification::route('mail', config('gulraiz.admin_email'))->notify(new EditRequestSubmitted($editRequest));

        return to_route('portal.orders.show', $order)->with('status', __('Edit request sent. You’ll get an email when it’s done.'));
    }
}
