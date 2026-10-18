<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function show(Category $category): View
    {
        abort_unless($category->is_active, 404);

        $category->load(['products' => function ($q) {
            $q->where('is_active', true)->with('images')->orderBy('sort_order');
        }]);

        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $settings = Setting::pluck('value', 'key');

        return view('pages.category', compact('category', 'categories', 'settings'));
    }
}

