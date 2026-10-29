<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function index(): View
    {
        return view('admin.contact-messages.index', [
            'contactMessages' => ContactMessage::query()->latest()->paginate(25),
        ]);
    }

    public function show(ContactMessage $contactMessage): View
    {
        if ($contactMessage->read_at === null) {
            $contactMessage->forceFill(['read_at' => now()])->save();
        }

        return view('admin.contact-messages.show', ['contactMessage' => $contactMessage]);
    }

    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->delete();

        return to_route('admin.contact-messages.index')->with('status', __('Message deleted.'));
    }
}
