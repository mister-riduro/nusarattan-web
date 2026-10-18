<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Primary SEO Meta Tags -->
    <title>@yield('title', 'Indonesian Rattan Furniture Exporter | Wholesale & Custom Manufacturer')</title>
    <meta name="title" content="@yield('title', 'Indonesian Rattan Furniture Exporter | Wholesale & Custom Manufacturer')">
    <meta name="description" content="@yield('meta_description', 'Nusarattan is a trusted Indonesian rattan furniture manufacturer and exporters specializing in hotels, villas, restaurants, and commercial projects. Custom designs, wholesale export, eco-friendly material, handcrafted by local artisans.')">
    <meta name="robots" content="@yield('meta_robots', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1')">
    <meta name="author" content="Nusarattan">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <!-- Open Graph / Facebook / LinkedIn / WhatsApp -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:site_name" content="Nusarattan">
    <meta property="og:title" content="@yield('og_title', View::getSection('title') ?? 'Indonesian Rattan Furniture Exporter | Wholesale & Custom Manufacturer')">
    <meta property="og:description" content="@yield('og_description', View::getSection('meta_description') ?? 'Nusarattan is a trusted Indonesian rattan furniture manufacturer and wholesale exporter.')">
    <meta property="og:image" content="@yield('og_image', asset('storage/products/fb6a6120-812c-4ad1-95a4-30c2b51e00a6.webp'))">
    <meta property="og:image:alt" content="@yield('og_image_alt', 'Nusarattan Handcrafted Rattan Furniture')">
    <meta property="og:locale" content="en_US">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="@yield('canonical', url()->current())">
    <meta name="twitter:title" content="@yield('og_title', View::getSection('title') ?? 'Indonesian Rattan Furniture Exporter | Wholesale & Custom Manufacturer')">
    <meta name="twitter:description" content="@yield('og_description', View::getSection('meta_description') ?? 'Nusarattan is a trusted Indonesian rattan furniture manufacturer and wholesale exporter.')">
    <meta name="twitter:image" content="@yield('og_image', asset('storage/products/fb6a6120-812c-4ad1-95a4-30c2b51e00a6.webp'))">

    <!-- Geographic & International Target Metadata -->
    <meta name="geo.region" content="ID-JT">
    <meta name="geo.placename" content="Sukoharjo, Central Java, Indonesia">
    <meta name="geo.position" content="-7.6083;110.8711">
    <meta name="ICBM" content="-7.6083, 110.8711">

    @if(config('services.seo.google_verification'))
        <meta name="google-site-verification" content="{{ config('services.seo.google_verification') }}">
    @endif
    @if(config('services.seo.bing_verification'))
        <meta name="msvalidate.01" content="{{ config('services.seo.bing_verification') }}">
    @endif

    <!-- Schema.org JSON-LD Structured Data: Global Business Entity -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@type": ["Organization", "Manufacturer"],
      "name": "Nusarattan",
      "legalName": "PT Rekayasa Karya Utama",
      "url": "{{ url('/') }}",
      "logo": "{{ asset('storage/site/UPROaBi4W9KCVokCF8lnVYonS0.webp') }}",
      "image": "{{ asset('storage/products/fb6a6120-812c-4ad1-95a4-30c2b51e00a6.webp') }}",
      "description": "Indonesian rattan furniture exporter and wholesale manufacturer specializing in custom designs and sustainable natural craftsmanship for hospitality and commercial projects.",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Mendungan RT 001 / RW 004 Pabelan, Kartasura",
        "addressLocality": "Sukoharjo",
        "addressRegion": "Jawa Tengah",
        "postalCode": "57169",
        "addressCountry": "ID"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": -7.6083,
        "longitude": 110.8711
      },
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+6282228764381",
        "contactType": "sales",
        "availableLanguage": ["English", "Indonesian"]
      }
    }
    </script>
    @yield('schema_json')

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FFF8ED] text-[#333333] font-sans antialiased selection:bg-[#693D22] selection:text-white"
      x-data="{ 
          catalogModalOpen: false, 
          videoModalOpen: false, 
          mobileMenuOpen: false,
          scrolled: false,
          activeCatalogUrl: '{{ \App\Models\Setting::get('catalog_url', '/catalog.pdf') }}',
          openCatalogModal() {
              this.catalogModalOpen = true;
          },
          openVideoModal() {
              this.videoModalOpen = true;
          }
      }"
      @scroll.window="scrolled = (window.pageYOffset > 20)">

    <x-navbar />

    <main>
        @yield('content')
    </main>

    <x-footer />

    <!-- Download Catalog Modal -->
    <x-download-catalog-modal />

    <!-- Watch Video Modal -->
    <x-video-modal />

</body>
</html>

