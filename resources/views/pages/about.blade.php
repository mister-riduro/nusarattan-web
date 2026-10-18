@extends('layouts.app')

@php
    $aboutImage = $settings['about_hero_image'] ?? asset('storage/products/fb6a6120-812c-4ad1-95a4-30c2b51e00a6.webp');
@endphp


@section('title', 'About Nusarattan | Indonesian Rattan Furniture Manufacturer')
@section('meta_description', 'Learn about Nusarattan, trusted Indonesian rattan furniture exporter and manufacturer specializing in custom designs and sustainable craftsmanship.')
@section('og_image', $aboutImage)
@section('og_image_alt', 'About Nusarattan - Indonesian Rattan Furniture Manufacturer')

@section('schema_json')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Home",
      "item": "{{ url('/') }}"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "About Us",
      "item": "{{ url()->current() }}"
    }
  ]
}
</script>
@endsection


@section('content')
<!-- Hero Section -->
<section class="pt-28 pb-16 lg:pt-36 lg:pb-24 bg-[#FFF8ED] overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <!-- Left Headline -->
            <div class="lg:col-span-6 pr-0 lg:pr-8">
                <h1 class="font-serif text-4xl sm:text-5xl lg:text-[54px] font-normal text-[#222222] mb-6">
                    About Us
                </h1>
                <p class="text-base sm:text-lg text-[#555555] font-light leading-relaxed max-w-lg">
                    Nusarattan is a trusted Indonesia rattan furniture manufacturer and exporters. We are specialize in crafting premium pieces for hotels, villas, restaurants, and commercial projects worldwide.
                </p>
            </div>

            <!-- Right Image: Artisan at work -->
            <div class="lg:col-span-6">
                <div class="rounded-3xl overflow-hidden shadow-sm max-w-lg mx-auto aspect-[4/5] bg-[#FAF6F0]">
                    <img src="{{ $settings['about_hero_image'] ?? '/placeholder.png' }}" 
                         alt="Nusarattan artisan crafting furniture"
                         width="600" height="750" 
                         class="w-full h-full object-cover">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Commitment Section -->
<section class="py-24 sm:py-32 bg-white overflow-hidden">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center">
        <!-- Top Tilted Photo -->
        <div class="mb-14 flex justify-center">
            <div class="transform -rotate-6 hover:rotate-0 transition-transform duration-500 rounded-2xl overflow-hidden shadow-xl max-w-[280px] sm:max-w-[320px] aspect-[4/3] bg-[#FAF6F0] border-4 border-white">
                <img src="{{ $settings['about_commit_1'] ?? '/placeholder.png' }}" 
                     alt="Artisan weaving rattan"
                     width="400" height="300"
                     class="w-full h-full object-cover">
            </div>
        </div>

        <!-- Commitment Text -->
        <h2 class="font-serif text-3xl sm:text-4xl text-[#222222] font-normal mb-6">
            Our Commitment
        </h2>

        <p class="text-sm sm:text-base text-[#666666] leading-relaxed max-w-2xl mx-auto font-light">
            Our commitment to excellence includes offering custom designs to your vision, wholesale export solutions and the use of eco-friendly materials sourced responsibly. Every piece is skillfully handcrafted by local Indonesia artisans, blending traditional craftsmanship with modern aesthetics to deliver sustainable, high quality furniture that elevates any space.
        </p>

        <!-- Bottom Tilted Photo -->
        <div class="mt-14 flex justify-center">
            <div class="transform rotate-6 hover:rotate-0 transition-transform duration-500 rounded-2xl overflow-hidden shadow-xl max-w-[280px] sm:max-w-[320px] aspect-[4/3] bg-[#FAF6F0] border-4 border-white">
                <img src="{{ $settings['about_commit_2'] ?? '/placeholder.png' }}" 
                     alt="Artisan with finished chair"
                     width="400" height="300"
                     class="w-full h-full object-cover">
            </div>
        </div>
    </div>
</section>

<!-- Our Gallery Section -->
<section class="py-20 sm:py-28 bg-[#FFF8ED]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-baseline mb-16">
            <div class="md:col-span-4">
                <h2 class="font-serif text-3xl sm:text-4xl text-[#222222] font-normal">
                    Our Gallery
                </h2>
            </div>
            <div class="md:col-span-8">
                <p class="text-sm sm:text-base text-[#666666] font-light leading-relaxed max-w-2xl">
                    Explore how our work comes to life. This gallery features both the making process and the completed creations crafted by our skilled Indonesian artisans.
                </p>
            </div>
        </div>

        <!-- Gallery Grid: 2 Columns of photos -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 sm:gap-8 items-start">
            @foreach($gallery as $index => $item)
                <div class="relative group rounded-2xl overflow-hidden bg-white shadow-sm border border-[#8888881A]">
                    <img src="{{ $item['url'] }}" 
                         alt="{{ $item['caption'] ?? 'Nusarattan gallery' }}" 
                         loading="lazy"
                         width="600" height="600"
                         class="w-full h-auto object-cover group-hover:scale-102 transition-transform duration-300">
                    
                    @if(!empty($item['badge']))
                        <div class="absolute bottom-4 right-4 bg-white/90 backdrop-blur-sm px-4 py-1.5 rounded-full shadow text-xs font-semibold text-[#222222]">
                            {{ $item['badge'] }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Contact Form Component -->
<x-contact-form />
@endsection

