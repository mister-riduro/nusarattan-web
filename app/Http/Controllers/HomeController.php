<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FurnitureSet;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $furnitureSet = FurnitureSet::with(['products.images', 'products' => function ($q) {
            $q->orderByPivot('sort_order');
        }])->where('is_active', true)->orderBy('sort_order')->first();

        $featuredProducts = Product::with('images')
            ->where('is_featured', true)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $settings = Setting::pluck('value', 'key');

        return view('pages.home', compact('furnitureSet', 'featuredProducts', 'categories', 'settings'));
    }
}

