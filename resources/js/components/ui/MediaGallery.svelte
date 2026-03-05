<script>
    import { createRawSnippet } from 'svelte';

    let { 
        media = [], 
        class: className = '' 
    } = $props();

    const images = $derived(media.filter(m => m.type === 'property_image'));
    let activeImage = $state(0);

    function nextImage() {
        activeImage = (activeImage + 1) % images.length;
    }

    function prevImage() {
        activeImage = (activeImage - 1 + images.length) % images.length;
    }
</script>

<div class="space-y-4 {className}">
    <div class="h-[500px] bg-white rounded-2xl flex items-center justify-center border border-gray-200 shadow-sm overflow-hidden relative group">
        {#if images.length > 0}
            <img 
                src="/storage/{images[activeImage].path}" 
                alt="Property View" 
                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" 
            />
            
            {#if images.length > 1}
                <div class="absolute inset-0 flex items-center justify-between p-4 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button 
                        class="w-12 h-12 bg-white/90 rounded-full flex items-center justify-center shadow-lg hover:bg-brand-action hover:text-white transition-all"
                        onclick={prevImage}
                        aria-label="Previous image"
                    >
                        <span class="material-symbols-outlined">chevron_left</span>
                    </button>
                    <button 
                        class="w-12 h-12 bg-white/90 rounded-full flex items-center justify-center shadow-lg hover:bg-brand-action hover:text-white transition-all"
                        onclick={nextImage}
                        aria-label="Next image"
                    >
                        <span class="material-symbols-outlined">chevron_right</span>
                    </button>
                </div>
                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2">
                    {#each images as _, i}
                        <button 
                            class="w-2 h-2 rounded-full transition-all {i === activeImage ? 'w-8 bg-brand-action' : 'bg-white/50'}"
                            onclick={() => activeImage = i}
                            aria-label="View image {i + 1}"
                        ></button>
                    {/each}
                </div>
            {/if}
        {:else}
            <div class="flex flex-col items-center gap-4 text-gray-300">
                <span class="material-symbols-outlined text-9xl">image</span>
                <span class="font-black uppercase tracking-widest text-xs">No images available</span>
            </div>
        {/if}
    </div>

    {#if images.length > 1}
        <div class="flex gap-4 overflow-x-auto pb-2 scrollbar-hide">
            {#each images as img, i}
                <button 
                    class="flex-shrink-0 w-24 aspect-square rounded-xl overflow-hidden border-2 transition-all {i === activeImage ? 'border-brand-action shadow-md' : 'border-transparent opacity-60 hover:opacity-100'}"
                    onclick={() => activeImage = i}
                    aria-label="Select thumbnail {i + 1}"
                >
                    <img src="/storage/{img.path}" alt="Thumb" class="w-full h-full object-cover" />
                </button>
            {/each}
        </div>
    {/if}
</div>
