<div x-show="videoModalOpen" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="video-modal-title" role="dialog" aria-modal="true">
    
    <!-- Backdrop -->
    <div x-show="videoModalOpen" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/80 transition-opacity"
         @click="videoModalOpen = false"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <!-- Video panel -->
        <div x-show="videoModalOpen" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.away="videoModalOpen = false"
             class="relative transform overflow-hidden rounded-2xl bg-black text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl">
            
            <!-- Close Button -->
            <button @click="videoModalOpen = false" 
                    type="button" 
                    class="absolute top-3 right-3 z-10 text-white/70 hover:text-white bg-black/50 hover:bg-black/80 rounded-full p-2 transition-colors"
                    aria-label="Close video">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- YouTube responsive embed -->
            <div class="relative w-full aspect-video">
                <template x-if="videoModalOpen">
                    <iframe 
                        class="w-full h-full"
                        src="https://www.youtube.com/embed/{{ \App\Models\Setting::get('youtube_id', '8AHPXm9Y6mI') }}?autoplay=1&rel=0" 
                        title="Nusarattan Video" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                        allowfullscreen>
                    </iframe>
                </template>
            </div>
        </div>
    </div>
</div>

