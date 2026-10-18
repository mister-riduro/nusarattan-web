<div x-show="catalogModalOpen" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <!-- Backdrop -->
    <div x-show="catalogModalOpen" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/60 transition-opacity"
         @click="catalogModalOpen = false"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <!-- Modal panel -->
        <div x-show="catalogModalOpen" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.away="catalogModalOpen = false"
             x-data="{
                 name: '',
                 business_type: '',
                 email: '',
                 website_hp: '',
                 submitting: false,
                 submitted: false,
                 errorMsg: '',
                 async submitCatalogForm() {
                     this.submitting = true;
                     this.errorMsg = '';
                     try {
                         const response = await fetch('{{ route('contact.store') }}', {
                             method: 'POST',
                             headers: {
                                 'Content-Type': 'application/json',
                                 'Accept': 'application/json',
                                 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                             },
                             body: JSON.stringify({
                                 type: 'catalog_download',
                                 name: this.name,
                                 business_type: this.business_type,
                                 email: this.email,
                                 website_hp: this.website_hp
                             })
                         });
                         const data = await response.json();
                         if (response.ok && data.success) {
                             this.submitted = true;
                             // Trigger catalog file download
                             const link = document.createElement('a');
                             link.href = data.catalog_url || activeCatalogUrl;
                             link.download = 'nusarattan-catalog.pdf';
                             link.target = '_blank';
                             document.body.appendChild(link);
                             link.click();
                             document.body.removeChild(link);
                             setTimeout(() => {
                                 catalogModalOpen = false;
                                 this.submitted = false;
                                 this.name = '';
                                 this.business_type = '';
                                 this.email = '';
                             }, 2500);
                         } else {
                             this.errorMsg = data.message || 'Something went wrong. Please check your inputs.';
                         }
                     } catch (e) {
                         this.errorMsg = 'Network error. Please try again.';
                     } finally {
                         this.submitting = false;
                     }
                 }
             }"
             class="relative transform overflow-hidden rounded-[24px] bg-white px-6 pt-10 pb-8 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-10">
            
            <!-- Close Button -->
            <button @click="catalogModalOpen = false" 
                    type="button" 
                    class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition-colors p-1"
                    aria-label="Close modal">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Title -->
            <div class="text-center mb-8">
                <h3 class="font-serif text-2xl sm:text-3xl text-[#222222] leading-snug">
                    Before downloading the catalog, we’d love to learn a bit more about you.
                </h3>
            </div>

            <div x-show="submitted" class="text-center py-6">
                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h4 class="text-lg font-semibold text-gray-800">Downloading your catalog...</h4>
                <p class="text-sm text-gray-500 mt-1">Thank you for your interest in Nusarattan!</p>
            </div>

            <!-- Form -->
            <form x-show="!submitted" @submit.prevent="submitCatalogForm()" class="space-y-4">
                <!-- Honeypot -->
                <input type="text" name="website_hp" x-model="website_hp" class="hidden" tabindex="-1" autocomplete="off">

                <div x-show="errorMsg" class="p-3 bg-red-50 text-red-600 rounded-xl text-sm" x-text="errorMsg"></div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Name</label>
                    <input type="text" 
                           x-model="name" 
                           required 
                           placeholder="Jane Smith"
                           class="w-full px-4 py-3 bg-[#F6F6F6] border border-transparent rounded-xl text-sm text-[#333333] placeholder-gray-400 focus:bg-white focus:border-[#693D22] focus:ring-0 outline-none transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Business Type</label>
                    <input type="text" 
                           x-model="business_type" 
                           placeholder="e.g Trader, Gallery, Exporter"
                           class="w-full px-4 py-3 bg-[#F6F6F6] border border-transparent rounded-xl text-sm text-[#333333] placeholder-gray-400 focus:bg-white focus:border-[#693D22] focus:ring-0 outline-none transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                    <input type="email" 
                           x-model="email" 
                           required 
                           placeholder="jane@mail.com"
                           class="w-full px-4 py-3 bg-[#F6F6F6] border border-transparent rounded-xl text-sm text-[#333333] placeholder-gray-400 focus:bg-white focus:border-[#693D22] focus:ring-0 outline-none transition-colors">
                </div>

                <div class="pt-2">
                    <button type="submit" 
                            :disabled="submitting"
                            class="w-full py-3.5 px-6 bg-[#2B2B2B] hover:bg-black text-white text-sm font-semibold rounded-xl shadow transition-all duration-200 disabled:opacity-50">
                        <span x-show="!submitting">Submit</span>
                        <span x-show="submitting" x-cloak>Processing...</span>
                    </button>
                </div>

                <p class="text-[11px] text-gray-400 text-center leading-normal pt-2">
                    We respect your privacy and will NOT share your contact details or personal data with third parties.
                </p>
            </form>
        </div>
    </div>
</div>

