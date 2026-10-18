<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Indonesian Rattan Furniture Exporter | Wholesale & Custom Manufacturer')</title>
    <meta name="description" content="@yield('meta_description', 'Nusarattan is trusted Indonesia rattan furniture manufacturer and exporters specializing in hotels, villas, restaurants and commercial projects. Custom designs, wholesale export, eco-friendly material, local artisans')">
    <link rel="canonical" href="{{ url()->current() }}">

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

