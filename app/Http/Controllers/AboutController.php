<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function __invoke(): View
    {
        $photoPath = Setting::get('about_photo_path');

        return view('pages.about', [
            'heading' => Setting::get('about_heading', __('Hi, I’m Mohammed Gulraiz.')),
            'body' => Setting::get('about_body', __('I build fast, good-looking websites for small businesses, and I look after them so you don’t have to.')),
            'photoUrl' => $photoPath ? Storage::disk('public')->url($photoPath) : null,
        ]);
    }
}
