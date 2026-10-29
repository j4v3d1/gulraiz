<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use App\Notifications\ContactMessageReceived;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('pages.contact');
    }

    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        $contactMessage = ContactMessage::create($request->safe()->except('company_website'));

        Notification::route('mail', config('gulraiz.admin_email'))->notify(new ContactMessageReceived($contactMessage));

        return to_route('contact.create')->with('status', __('Thanks! Your message has been sent. I’ll get back to you within one business day.'));
    }
}
