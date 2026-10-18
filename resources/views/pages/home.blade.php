@extends('layouts.app')

@section('title', 'Indonesian Rattan Furniture Exporter | Wholesale & Custom Manufacturer')
@section('meta_description', 'Nusarattan is trusted Indonesia rattan furniture manufacturer and exporters specializing in hotels, villas, restaurants and commercial projects. Custom designs, wholesale export, eco-friendly material, local artisans')

@section('content')
<!-- Hero Section -->
<section class="pt-28 pb-16 lg:pt-36 lg:pb-24 overflow-hidden bg-[#FFF8ED]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-12 items-center">
            <!-- Left Column: Headline and CTAs (6 cols = 50%) -->
            <div class="lg:col-span-6 flex flex-col items-start pr-0 lg:pr-6">
                <h1 class="font-serif text-3xl sm:text-4xl lg:text-[44px] xl:text-[48px] font-normal text-[#222222] leading-[1.18] tracking-tight">
                    {{ $settings['hero_heading'] ?? 'Indonesian Rattan Furniture Exporter, Wholesale and Custom Manufacturer' }}
                </h1>
                
                <p class="text-base sm:text-lg text-[#666666] font-light mt-6 mb-8 max-w-lg leading-relaxed">
                    {{ $settings['hero_subheading'] ?? 'We deliver premium and sustainable rattan products worldwide.' }}
                </p>

                <!-- CTA Action Buttons -->
                <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                    <!-- Download Catalog Button -->
                    <button @click="openCatalogModal()" 
                            type="button"
                            class="inline-flex items-center gap-2.5 px-6 py-3.5 bg-[#4A2C1D] hover:bg-[#381F13] text-white text-sm font-medium rounded-full shadow-md transition-all duration-200">
                        <!-- Catalog Icon -->
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M 5 21 C 4.45 21 3.979 20.804 3.587 20.413 C 3.196 20.021 3 19.55 3 19 L 3 5 C 3 4.45 3.196 3.979 3.587 3.587 C 3.979 3.196 4.45 3 5 3 L 19 3 C 19.55 3 20.021 3.196 20.413 3.587 C 20.804 3.979 21 4.45 21 5 L 21 19 C 21 19.55 20.804 20.021 20.413 20.413 C 20.021 20.804 19.55 21 19 21 Z M 7 9 L 17 9 L 17 7 L 7 7 Z M 7 13 L 17 13 L 17 11 L 7 11 Z M 7 17 L 14 17 L 14 15 L 7 15 Z"/>
                        </svg>
                        <span>Download Catalog</span>
                    </button>

                    <!-- Watch our Video Button -->
                    <button @click="openVideoModal()" 
                            type="button"
                            class="inline-flex items-center gap-2 px-4 py-3.5 text-[#693D22] hover:text-[#4A2C1D] text-sm font-semibold transition-colors duration-200">
                        <!-- Play circle icon -->
                        <svg class="w-6 h-6 text-[#693D22] fill-current" viewBox="0 0 24 24">
                            <path d="M 10.65 15.75 L 15.525 12.625 C 15.758 12.475 15.875 12.267 15.875 12 C 15.875 11.733 15.758 11.525 15.525 11.375 L 10.65 8.25 C 10.4 8.083 10.146 8.071 9.887 8.212 C 9.629 8.354 9.5 8.575 9.5 8.875 L 9.5 15.125 C 9.5 15.425 9.629 15.646 9.887 15.788 C 10.146 15.929 10.4 15.917 10.65 15.75 Z M 12 22 C 10.617 22 9.317 21.738 8.1 21.212 C 6.883 20.688 5.825 19.975 4.925 19.075 C 4.025 18.175 3.312 17.117 2.788 15.9 C 2.262 14.683 2 13.383 2 12 C 2 10.617 2.262 9.317 2.788 8.1 C 3.312 6.883 4.025 5.825 4.925 4.925 C 5.825 4.025 6.883 3.312 8.1 2.788 C 9.317 2.262 10.617 2 12 2 C 13.383 2 14.683 2.262 15.9 2.788 C 17.117 3.312 18.175 4.025 19.075 4.925 C 19.975 5.825 20.688 6.883 21.212 8.1 C 21.738 9.317 22 10.617 22 12 C 22 13.383 21.738 14.683 21.212 15.9 C 20.688 17.117 19.975 18.175 19.075 19.075 C 18.175 19.975 17.117 20.688 15.9 21.212 C 14.683 21.738 13.383 22 12 22 Z"/>
                        </svg>
                        <span>Watch our Video</span>
                    </button>
                </div>
            </div>

            <!-- Right Column: Hero Image (at least 50% width on desktop) -->
            <div class="lg:col-span-6 relative w-full flex justify-center lg:justify-end">
                <div class="relative w-full aspect-[4/5] sm:aspect-[1/1] lg:aspect-[4/5] xl:aspect-[3/4] rounded-3xl overflow-hidden shadow-sm">
                    <img src="{{ $settings['hero_image_url'] ?? '/placeholder.png' }}" 
                         alt="Nusarattan handcrafted chair" 
                         width="700" height="875"
                         class="w-full h-full object-cover">
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Furniture Set Component -->
@if($furnitureSet)
    <x-furniture-set :set="$furnitureSet" />
@endif

<!-- Why Choose Nusarattan -->
<x-why-choose />

<!-- Featured Products Section -->
<section class="py-20 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="font-serif text-3xl sm:text-4xl text-[#222222] font-normal">
                Featured Products
            </h2>
        </div>

        <!-- 8 Products Grid: 4 columns on desktop, 2 on mobile/tablet -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
            @foreach($featuredProducts as $product)
                <x-product-card :product="$product" variant="featured" />
            @endforeach
        </div>

        <!-- Bottom Actions -->
        <div class="mt-14 flex flex-wrap items-center justify-center gap-4">
            <a href="{{ route('category.show', 'indoor') }}" 
               class="px-8 py-3.5 bg-[#693D22] hover:bg-[#522E18] text-white text-sm font-medium rounded-full shadow-md transition-colors duration-200">
                See More Product
            </a>
            
            <button @click="openCatalogModal()" 
                    type="button"
                    class="px-8 py-3.5 bg-transparent hover:bg-gray-50 border border-[#693D22] text-[#693D22] text-sm font-medium rounded-full transition-colors duration-200">
                Download Catalog
            </button>
        </div>
    </div>
</section>

<!-- Contact Us Form Component -->
<x-contact-form />
@endsection

