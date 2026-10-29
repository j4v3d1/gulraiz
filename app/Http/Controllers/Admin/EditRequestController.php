<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EditRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateEditRequestRequest;
use App\Models\EditRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EditRequestController extends Controller
{
    public function index(Request $request): View
    {
        $status = EditRequestStatus::tryFrom($request->string('status')->toString());

        return view('admin.edit-requests.index', [
            'editRequests' => EditRequest::query()
                ->with('order.user')
                ->when(
                    $status,
                    fn ($query) => $query->where('status', $status),
                    fn ($query) => $query->where('status', '!=', EditRequestStatus::Done),
                )
                ->oldest()
                ->paginate(25)
                ->withQueryString(),
            'selectedStatus' => $status,
        ]);
    }

    public function update(UpdateEditRequestRequest $request, EditRequest $editRequest): RedirectResponse
    {
        $status = $request->enum('status', EditRequestStatus::class);

        $editRequest->update([
            'status' => $status,
            'admin_notes' => $request->validated('admin_notes'),
            'completed_at' => $status === EditRequestStatus::Done ? ($editRequest->completed_at ?? now()) : null,
        ]);

        return back()->with('status', __('Edit request updated.'));
    }
}
