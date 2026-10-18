<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        $settings = Setting::pluck('value', 'key');
        $gallery = json_decode($settings['about_gallery_json'] ?? '[]', true);
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        return view('pages.about', compact('settings', 'gallery', 'categories'));
    }
}

