<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAboutRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function edit(): View
    {
        $photoPath = Setting::get('about_photo_path');

        return view('admin.about.edit', [
            'heading' => Setting::get('about_heading'),
            'body' => Setting::get('about_body'),
            'photoUrl' => $photoPath ? Storage::disk('public')->url($photoPath) : null,
        ]);
    }

    public function update(UpdateAboutRequest $request): RedirectResponse
    {
        Setting::put('about_heading', $request->validated('about_heading'));
        Setting::put('about_body', $request->validated('about_body'));

        if ($request->hasFile('about_photo')) {
            if ($oldPath = Setting::get('about_photo_path')) {
                Storage::disk('public')->delete($oldPath);
            }

            Setting::put('about_photo_path', $request->file('about_photo')->store('about', 'public'));
        }

        return to_route('admin.about.edit')->with('status', __('About page updated.'));
    }
}
