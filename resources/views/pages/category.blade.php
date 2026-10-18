@extends('layouts.app')

@section('title', $category->meta_title ?? "{$category->name} | Rattan Furniture by Nusarattan")
@section('meta_description', $category->meta_description ?? "Browse {$category->name} from Nusarattan, Indonesian rattan furniture manufacturer. Custom designs, wholesale export, handcrafted by local artisans.")

@section('content')
<!-- Category Snippets Header -->
<section class="pt-28 pb-14 lg:pt-36 lg:pb-16 bg-[#FFF8ED]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="font-serif text-3xl sm:text-4xl lg:text-[46px] text-[#222222] font-normal mb-4">
            Our Products Snippets
        </h1>
        
        <p class="text-sm sm:text-base text-[#666666] max-w-xl mx-auto leading-relaxed mb-8">
            A glimpse of our finest rattan products, thoughtfully crafted to balance beauty, comfort, and sustainability.
        </p>

        <!-- Download Catalog Pill -->
        <div class="mb-12">
            <button @click="openCatalogModal()" 
                    type="button"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-[#4A2C1D] hover:bg-[#381F13] text-white text-xs sm:text-sm font-medium rounded-full shadow-md transition-all duration-200">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path d="M 5 21 C 4.45 21 3.979 20.804 3.587 20.413 C 3.196 20.021 3 19.55 3 19 L 3 5 C 3 4.45 3.196 3.979 3.587 3.587 C 3.979 3.196 4.45 3 5 3 L 19 3 C 19.55 3 20.021 3.196 20.413 3.587 C 20.804 3.979 21 4.45 21 5 L 21 19 C 21 19.55 20.804 20.021 20.413 20.413 C 20.021 20.804 19.55 21 19 21 Z M 7 9 L 17 9 L 17 7 L 7 7 Z M 7 13 L 17 13 L 17 11 L 7 11 Z M 7 17 L 14 17 L 14 15 L 7 15 Z"/>
                </svg>
                <span>Download Catalog</span>
            </button>
        </div>

        <!-- 6 Category Switcher Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
            @foreach($categories as $cat)
                @php
                    $thumbUrl = \App\Models\Setting::get('category_thumb_' . $cat->slug) 
                        ?: ($cat->products->first()?->primaryImage?->url ?? '/placeholder.png');
                    $isActive = $cat->id === $category->id;
                @endphp
                <a href="{{ route('category.show', $cat->slug) }}" 
                   class="group relative h-36 sm:h-44 rounded-2xl overflow-hidden block transition-all duration-200 {{ $isActive ? 'ring-3 ring-[#693D22] scale-[1.02] shadow-md' : 'opacity-90 hover:opacity-100 hover:scale-[1.01]' }}">
                    <!-- Background Image -->
                    <img src="{{ $thumbUrl }}" 
                         alt="{{ $cat->name }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    
                    <!-- Dark Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                    
                    <!-- Category Name -->
                    <div class="absolute bottom-3 left-3 right-3 text-left">
                        <span class="text-white text-xs sm:text-sm font-semibold leading-tight line-clamp-2 block drop-shadow-sm">
                            {{ $cat->name }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Products of Current Category -->
<section class="py-16 sm:py-24 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Category Title -->
        <div class="text-center mb-12 sm:mb-16">
            <h2 class="font-serif text-3xl sm:text-4xl text-[#222222] font-normal">
                {{ $category->name }}
            </h2>
        </div>

        <!-- 2 Columns Grid of Products -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 sm:gap-10">
            @forelse($category->products as $product)
                <x-product-card :product="$product" variant="category" />
            @empty
                <div class="col-span-2 text-center py-12 text-gray-500">
                    No products currently available in this category.
                </div>
            @endforelse
        </div>

        <!-- Bottom Catalog CTA -->
        <div class="mt-20 text-center space-y-4">
            <h3 class="font-serif text-xl sm:text-2xl text-[#222222] font-normal">
                Want to see all our products?
            </h3>
            <div>
                <button @click="openCatalogModal()" 
                        type="button"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-[#4A2C1D] hover:bg-[#381F13] text-white text-xs sm:text-sm font-medium rounded-full shadow-md transition-all duration-200">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                        <path d="M 5 21 C 4.45 21 3.979 20.804 3.587 20.413 C 3.196 20.021 3 19.55 3 19 L 3 5 C 3 4.45 3.196 3.979 3.587 3.587 C 3.979 3.196 4.45 3 5 3 L 19 3 C 19.55 3 20.021 3.196 20.413 3.587 C 20.804 3.979 21 4.45 21 5 L 21 19 C 21 19.55 20.804 20.021 20.413 20.413 C 20.021 20.804 19.55 21 19 21 Z M 7 9 L 17 9 L 17 7 L 7 7 Z M 7 13 L 17 13 L 17 11 L 7 11 Z M 7 17 L 14 17 L 14 15 L 7 15 Z"/>
                    </svg>
                    <span>Download Catalog</span>
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Component -->
<x-contact-form />
@endsection

