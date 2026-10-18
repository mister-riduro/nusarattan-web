<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $baseUrl = config('app.env') === 'production'
            ? 'https://www.nusarattan.com'
            : config('app.url', 'https://www.nusarattan.com');

        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $content = view('sitemap', [
            'baseUrl' => rtrim($baseUrl, '/'),
            'categories' => $categories,
        ])->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }
}

