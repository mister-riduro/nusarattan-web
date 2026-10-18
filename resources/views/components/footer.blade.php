<footer class="bg-white border-t border-[#8888881A] pt-16 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            <!-- Left Info -->
            <div class="lg:col-span-6 space-y-6">
                <!-- Logo -->
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 flex-shrink-0">
                        <img src="{{ \App\Models\Setting::get('logo_icon_url', '/storage/site/UPROaBi4W9KCVokCF8lnVYonS0.webp') }}" 
                             alt="Nusarattan" 
                             class="w-full h-full object-contain"
                             width="40" height="40">
                    </div>
                </div>

                <p class="font-serif text-2xl sm:text-3xl text-[#333333] max-w-md leading-snug">
                    {{ \App\Models\Setting::get('footer_tagline', 'Well-crafted rattan furniture from Nusantara (Indonesia archipelago).') }}
                </p>

                <!-- Footer Nav -->
                <div class="flex items-center space-x-6 text-[14px] text-[#555555] pt-4">
                    <a href="{{ route('home') }}" class="hover:text-[#693D22] transition-colors">Home</a>
                    <a href="{{ route('category.show', 'indoor') }}" class="hover:text-[#693D22] transition-colors">Product</a>
                    <a href="{{ route('about') }}" class="hover:text-[#693D22] transition-colors">About Us</a>
                    <a href="{{ route('home') }}#contact" class="hover:text-[#693D22] transition-colors">Contact</a>
                </div>
            </div>

            <!-- Right: Map -->
            <div class="lg:col-span-6">
                <div class="w-full h-[280px] rounded-2xl overflow-hidden border border-[#8888881A] shadow-sm relative">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3962.378940826978!2d108.5446979!3d-6.7235081!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6f1df0e78f9fcf%3A0x8e87d9bf5b2dbd46!2sPT%20Rekayasa%20Karya%20Utama!5e0!3m2!1sen!2sid!4v1700000000000!5m2!1sen!2sid" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Nusarattan Location">
                    </iframe>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="mt-16 pt-8 border-t border-[#8888881A] text-sm text-[#777777]">
            <p>{{ \App\Models\Setting::get('copyright_text', '© ' . date('Y') . ' PT Rekayasa Karya Utama') }}</p>
        </div>
    </div>
</footer>

