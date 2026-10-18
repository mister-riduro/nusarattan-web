<section id="contact" class="relative py-24 bg-cover bg-center overflow-hidden" 
         style="background-image: url('{{ \App\Models\Setting::get('contact_bg_url', '/storage/site/5wRxPWPSIln2LuBclV1cNDfyarI.webp') }}');">
    
    <!-- Dark overlay -->
    <div class="absolute inset-0 bg-black/65 backdrop-blur-[2px]"></div>

    <div class="relative max-w-xl mx-auto px-4 sm:px-6 z-10"
         x-data="{
             name: '',
             email: '',
             country: '',
             message: '',
             website_hp: '',
             submitting: false,
             success: false,
             errorMsg: '',
             async submitForm() {
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
                             type: 'contact',
                             name: this.name,
                             email: this.email,
                             country: this.country,
                             message: this.message,
                             website_hp: this.website_hp
                         })
                     });
                     const data = await response.json();
                     if (response.ok && data.success) {
                         this.success = true;
                         this.name = '';
                         this.email = '';
                         this.country = '';
                         this.message = '';
                     } else {
                         this.errorMsg = data.message || 'Something went wrong. Please check your inputs.';
                     }
                 } catch (e) {
                     this.errorMsg = 'Network error. Please try again.';
                 } finally {
                     this.submitting = false;
                 }
             }
         }">
        
        <div class="text-center mb-8">
            <h2 class="font-serif text-3xl sm:text-4xl text-white font-normal mb-3">
                Contact Us
            </h2>
            <p class="text-sm sm:text-base text-gray-200 font-light leading-relaxed">
                Get a quote, request custom designs, question to our team, or start your project? We’re just a message away. Let’s bring a touch of Nusarattan into your space.
            </p>
        </div>

        <div x-show="success" 
             x-cloak 
             class="bg-white/95 rounded-2xl p-8 text-center shadow-xl">
            <div class="w-12 h-12 bg-green-100 text-green-700 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-1">Message Sent!</h3>
            <p class="text-gray-600 text-sm">Thank you for reaching out. We will get back to you shortly.</p>
            <button @click="success = false" class="mt-4 px-5 py-2 bg-[#693D22] text-white text-xs font-medium rounded-full">Send another message</button>
        </div>

        <form x-show="!success" @submit.prevent="submitForm()" class="space-y-4">
            <!-- Honeypot -->
            <input type="text" name="website_hp" x-model="website_hp" class="hidden" tabindex="-1" autocomplete="off">

            <div x-show="errorMsg" class="p-3 bg-red-500/80 text-white rounded-xl text-sm" x-text="errorMsg"></div>

            <div>
                <label class="block text-xs font-medium text-gray-300 mb-1">Name</label>
                <input type="text" 
                       x-model="name" 
                       required 
                       placeholder="Jane Smith"
                       class="w-full px-4 py-3 bg-[#bbbbbb26] border border-[#88888833] rounded-xl text-sm text-white placeholder-gray-400 focus:border-[#9E582E] focus:bg-black/30 outline-none transition-colors">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-300 mb-1">Email</label>
                <input type="email" 
                       x-model="email" 
                       required 
                       placeholder="jane@mail.com"
                       class="w-full px-4 py-3 bg-[#bbbbbb26] border border-[#88888833] rounded-xl text-sm text-white placeholder-gray-400 focus:border-[#9E582E] focus:bg-black/30 outline-none transition-colors">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-300 mb-1">Country</label>
                <input type="text" 
                       x-model="country" 
                       placeholder="Where are you from?"
                       class="w-full px-4 py-3 bg-[#bbbbbb26] border border-[#88888833] rounded-xl text-sm text-white placeholder-gray-400 focus:border-[#9E582E] focus:bg-black/30 outline-none transition-colors">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-300 mb-1">Message</label>
                <textarea x-model="message" 
                          required 
                          rows="4" 
                          placeholder="Tell us about your inquiry"
                          class="w-full px-4 py-3 bg-[#bbbbbb26] border border-[#88888833] rounded-xl text-sm text-white placeholder-gray-400 focus:border-[#9E582E] focus:bg-black/30 outline-none transition-colors resize-y"></textarea>
            </div>

            <div class="pt-2">
                <button type="submit" 
                        :disabled="submitting"
                        class="w-full py-3.5 px-6 bg-[#9E582E] hover:bg-[#854520] text-white text-sm font-semibold rounded-xl shadow-lg transition-all duration-200 disabled:opacity-50">
                    <span x-show="!submitting">Submit</span>
                    <span x-show="submitting" x-cloak>Sending...</span>
                </button>
            </div>

            <p class="text-[11px] text-gray-400 text-center leading-normal pt-2">
                We respect your privacy and will NOT share your contact details or personal data with third parties.
            </p>
        </form>
    </div>
</section>

