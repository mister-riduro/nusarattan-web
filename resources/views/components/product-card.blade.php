@props(['product', 'variant' => 'featured'])

@php
    $imageUrl = $product->primaryImage?->url ?? '/placeholder.png';
@endphp

@if($variant === 'featured')
    <div class="group flex flex-col transition-all duration-300 hover:-translate-y-1">
        <!-- Image Container -->
        <div class="aspect-square w-full rounded-2xl overflow-hidden bg-[#FAF6F0] mb-3 border border-[#8888881A]">
            <img src="{{ $imageUrl }}" 
                 alt="{{ $product->name }}" 
                 loading="lazy"
                 width="400"
                 height="400"
                 class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500 ease-out">
        </div>
        
        <!-- Details -->
        <div class="flex flex-col space-y-1">
            <h3 class="font-bold text-base text-[#333333] tracking-wide">
                {{ $product->name }}
            </h3>
            @if($product->material)
                <p class="text-xs text-[#666666] leading-relaxed min-h-[32px]">
                    {{ $product->material }}
                </p>
            @endif
            @if($product->formatted_price)
                <p class="text-sm font-semibold text-[#333333] pt-1">
                    {{ $product->formatted_price }}
                </p>
            @endif
        </div>
    </div>
@else
    <!-- Category Page Variant: 2-column large landscape cards -->
    <div class="group flex flex-col space-y-3 transition-all duration-300">
        <div class="aspect-[16/10] sm:aspect-[16/11] w-full rounded-2xl overflow-hidden bg-[#FAF6F0] border border-[#8888881A]">
            <img src="{{ $imageUrl }}" 
                 alt="{{ $product->name }}" 
                 loading="lazy"
                 width="600"
                 height="400"
                 class="w-full h-full object-cover object-center group-hover:scale-102 transition-transform duration-500 ease-out">
        </div>
        <div>
            <h3 class="font-bold text-base sm:text-lg text-[#222222]">
                {{ $product->name }}
            </h3>
            @if($product->material)
                <p class="text-xs sm:text-sm text-[#777777] mt-0.5">
                    {{ $product->material }}
                </p>
            @endif
        </div>
    </div>
@endif

