<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product-{category:slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/about-us', [PageController::class, 'about'])->name('about');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::post('/contact', [InquiryController::class, 'store'])
    ->middleware('throttle:5,1')->name('contact.store');

