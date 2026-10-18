@props(['set'])

@php
    $whatsappNumber = preg_replace('/[^0-9]/', '', \App\Models\Setting::get('whatsapp_number', '6282228764381'));
    $waText = urlencode($set->whatsapp_message ?? 'Hi there, I would like to know the 01/14 Furniture set. Please give me the detail, Thanks!');
    $waUrl = "https://wa.me/{$whatsappNumber}?text={$waText}";
@endphp

<section class="py-16 sm:py-20 bg-white" 
         x-data="{ 
             activeItem: 'arm-chair',
             isDragging: false,
             startX: 0,
             scrollStart: 0,
             hasDragged: false,
             setActive(group) {
                 this.activeItem = group;
                 const el = document.getElementById('card-' + group);
                 const carousel = this.$refs.carousel;
                 if (el && carousel && window.innerWidth < 1024) {
                     const targetLeft = el.offsetLeft - carousel.offsetLeft - 16;
                     carousel.scrollTo({ left: Math.max(0, targetLeft), behavior: 'smooth' });
                 }
             },
             startDrag(e) {
                 if (window.innerWidth >= 1024) return;
                 this.isDragging = true;
                 this.hasDragged = false;
                 this.startX = e.pageX - this.$refs.carousel.offsetLeft;
                 this.scrollStart = this.$refs.carousel.scrollLeft;
             },
             onDrag(e) {
                 if (!this.isDragging || window.innerWidth >= 1024) return;
                 e.preventDefault();
                 const x = e.pageX - this.$refs.carousel.offsetLeft;
                 const walk = (x - this.startX) * 1.3;
                 if (Math.abs(x - this.startX) > 6) {
                     this.hasDragged = true;
                 }
                 this.$refs.carousel.scrollLeft = this.scrollStart - walk;
             },
             stopDrag() {
                 this.isDragging = false;
             },
             onWheel(e) {
                 if (window.innerWidth >= 1024) return;
                 const el = this.$refs.carousel;
                 if (!el) return;
                 const maxScroll = el.scrollWidth - el.clientWidth;
                 if (maxScroll > 0) {
                     if ((e.deltaY > 0 && el.scrollLeft < maxScroll - 5) || 
                         (e.deltaY < 0 && el.scrollLeft > 5)) {
                         el.scrollLeft += e.deltaY;
                     }
                 }
             },
             handleCardClick(group) {
                 if (this.hasDragged) return;
                 this.setActive(group);
             }
         }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- 2 Column Layout on Desktop, 1 Column on Tablet/Mobile -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start relative">
            
            <!-- Left: Highlighted Item (Title, Price, Full Set Image) -->
            <!-- Sticky on Desktop (lg:sticky lg:top-24 lg:self-start) so it travels along when scrolling down -->
            <div class="lg:col-span-8 lg:sticky lg:top-24 lg:self-start bg-[#FFF8ED] rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-[#8888881A]">
                <!-- Section Header with set name and prices -->
                <div class="flex items-baseline justify-between sm:justify-end mb-4 space-x-3 text-right">
                    <h2 class="font-script text-2xl sm:text-3xl font-bold text-[#333333]">
                        {{ $set->name }}
                    </h2>
                    <div class="flex items-center space-x-2 text-sm sm:text-base">
                        @if($set->price)
                            <span class="line-through text-[#999999] font-normal">{{ $set->formatted_price }}</span>
                        @endif
                        @if($set->sale_price)
                            <span class="font-medium text-[#555555]">{{ $set->formatted_sale_price }}</span>
                        @endif
                    </div>
                </div>

                <!-- Full Set Image with Hotspots -->
                <div class="relative rounded-xl overflow-hidden bg-white/50 select-none">
                    <img src="{{ $set->image_url }}" 
                         alt="{{ $set->name }}" 
                         class="w-full h-auto object-cover pointer-events-none"
                         width="900" height="600"
                         draggable="false">

                    <!-- Hotspot 1: Arm Chair (Right Chair: top 66.7%, left 77.8%) -->
                    <button @click="setActive('arm-chair')" 
                            type="button"
                            class="absolute top-[66.7%] left-[77.8%] -translate-x-1/2 -translate-y-1/2 group focus:outline-none cursor-pointer"
                            aria-label="View Arm Chair">
                        <span class="relative flex items-center justify-center">
                            <span class="inline-flex rounded-full h-5 w-5 bg-white shadow-md border-2 border-[#693D22] transition-all duration-200"
                                  :class="activeItem === 'arm-chair' ? 'scale-110 ring-4 ring-white/60' : 'hover:scale-110'"></span>
                        </span>
                    </button>

                    <!-- Hotspot 2: Sofa (Center: top 49.2%, left 67.3%) -->
                    <button @click="setActive('sofa')" 
                            type="button"
                            class="absolute top-[49.2%] left-[67.3%] -translate-x-1/2 -translate-y-1/2 group focus:outline-none cursor-pointer"
                            aria-label="View Sofa">
                        <span class="relative flex items-center justify-center">
                            <span class="inline-flex rounded-full h-5 w-5 bg-white shadow-md border-2 border-[#693D22] transition-all duration-200"
                                  :class="activeItem === 'sofa' ? 'scale-110 ring-4 ring-white/60' : 'hover:scale-110'"></span>
                        </span>
                    </button>

                    <!-- Hotspot 3: Table (Center Bottom: top 56.1%, left 45.9%) -->
                    <button @click="setActive('table')" 
                            type="button"
                            class="absolute top-[56.1%] left-[45.9%] -translate-x-1/2 -translate-y-1/2 group focus:outline-none cursor-pointer"
                            aria-label="View Coffee Table">
                        <span class="relative flex items-center justify-center">
                            <span class="inline-flex rounded-full h-5 w-5 bg-white shadow-md border-2 border-[#693D22] transition-all duration-200"
                                  :class="activeItem === 'table' ? 'scale-110 ring-4 ring-white/60' : 'hover:scale-110'"></span>
                        </span>
                    </button>
                </div>
            </div>

            <!-- Right: Sub Items List -->
            <!-- Horizontal scroll on mobile/tablet, vertical stack on desktop -->
            <div class="lg:col-span-4 flex flex-col space-y-6 min-w-0 w-full">
                <!-- Products container: mouse drag + wheel + touch swipe horizontal scroll on tablet/mobile -->
                <div x-ref="carousel"
                     @mousedown="startDrag($event)"
                     @mousemove="onDrag($event)"
                     @mouseup="stopDrag()"
                     @mouseleave="stopDrag()"
                     @wheel.passive="onWheel($event)"
                     class="flex flex-row lg:flex-col overflow-x-auto lg:overflow-visible gap-4 pb-4 lg:pb-0 snap-x snap-mandatory scroll-smooth no-scrollbar -mx-4 px-4 sm:-mx-6 sm:px-6 lg:mx-0 lg:px-0 select-none cursor-grab active:cursor-grabbing lg:cursor-default"
                     style="touch-action: pan-y;">
                    @foreach($set->products as $item)
                        @php
                            $group = $item->pivot->group ?? 'item';
                            $imgUrl = $item->primaryImage?->url ?? '/placeholder.png';
                        @endphp
                        <div id="card-{{ $group }}"
                             @click="handleCardClick('{{ $group }}')"
                             :class="activeItem === '{{ $group }}' ? 'border-[#693D22] shadow-md bg-[#FAF5EE]' : 'border-[#8888881A] bg-[#FAF6F0] hover:bg-[#FAF5EE]'"
                             class="cursor-pointer rounded-2xl p-4 border transition-all duration-200 w-[270px] sm:w-[300px] shrink-0 snap-start lg:w-full lg:shrink select-none">
                            <div class="aspect-square w-full rounded-xl overflow-hidden mb-3 bg-white pointer-events-none">
                                <img src="{{ $imgUrl }}" 
                                     alt="{{ $item->name }}" 
                                     loading="lazy"
                                     width="300" height="300"
                                     draggable="false"
                                     class="w-full h-full object-cover select-none pointer-events-none">
                            </div>
                            <div class="space-y-0.5 pointer-events-none">
                                <h3 class="font-bold text-sm sm:text-base text-[#222222]">
                                    {{ $item->name }}
                                </h3>
                                @if($item->dimensions)
                                    <p class="text-xs text-[#777777]">
                                        {{ $item->dimensions }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- WhatsApp CTA Button: Sits directly BELOW horizontal scroll on tablet/mobile, and below cards on desktop -->
                <div class="pt-2">
                    <a href="{{ $waUrl }}" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="inline-flex items-center justify-center w-full sm:w-auto px-7 py-3 rounded-full border border-[#222222] hover:bg-[#222222] hover:text-white text-sm font-medium text-[#222222] transition-colors duration-200 shadow-sm">
                        Contact for this set
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>
