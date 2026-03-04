<script>
    import { Home } from 'lucide-svelte';
    import Badge from '../ui/Badge.svelte';
    import Button from '../ui/Button.svelte';

    let { property, onBack, onScheduleVisit } = $props();

    const images = $derived(property.media?.filter(m => m.type === 'property_image') || []);
    const videos = $derived(property.media?.filter(m => m.type === 'property_video') || []);
    let activeImage = $state(0);
</script>

<div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
    <button 
        class="flex items-center gap-2 text-brand-primary/50 hover:text-brand-action font-bold transition-colors mb-4"
        onclick={onBack}
    >
        <span class="material-symbols-outlined">arrow_back</span>
        Back to Listings
    </button>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <div class="lg:col-span-2 space-y-8">
            <div class="space-y-4">
                <div class="h-[500px] bg-white rounded-2xl flex items-center justify-center border border-gray-200 shadow-sm overflow-hidden relative group">
                    {#if images.length > 0}
                        <img src="/storage/{images[activeImage].path}" alt={property.name} class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
                        
                        {#if images.length > 1}
                            <div class="absolute inset-0 flex items-center justify-between p-4 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button 
                                    class="w-12 h-12 bg-white/90 rounded-full flex items-center justify-center shadow-lg hover:bg-brand-action hover:text-white transition-all"
                                    onclick={() => activeImage = (activeImage - 1 + images.length) % images.length}
                                >
                                    <span class="material-symbols-outlined">chevron_left</span>
                                </button>
                                <button 
                                    class="w-12 h-12 bg-white/90 rounded-full flex items-center justify-center shadow-lg hover:bg-brand-action hover:text-white transition-all"
                                    onclick={() => activeImage = (activeImage + 1) % images.length}
                                >
                                    <span class="material-symbols-outlined">chevron_right</span>
                                </button>
                            </div>
                            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2">
                                {#each images as _, i}
                                    <button 
                                        class="w-2 h-2 rounded-full transition-all {i === activeImage ? 'w-8 bg-brand-action' : 'bg-white/50'}"
                                        onclick={() => activeImage = i}
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
                            >
                                <img src="/storage/{img.path}" alt="Thumb" class="w-full h-full object-cover" />
                            </button>
                        {/each}
                    </div>
                {/if}
            </div>
            
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <Badge type="primary">{property.type}</Badge>
                    <Badge type={property.status === 'available' ? 'success' : 'warning'}>
                        {property.status}
                    </Badge>
                </div>
                <h1 class="text-4xl font-extrabold tracking-tight text-brand-primary">{property.name}</h1>
                <p class="text-xl text-gray-500 flex items-center gap-2">
                    <span class="material-symbols-outlined text-brand-action">location_on</span>
                    {property.address}
                </p>
            </div>

            <div class="bg-white p-8 rounded-xl border border-gray-200 shadow-sm space-y-6">
                <h2 class="text-2xl font-bold">About this property</h2>
                <p class="text-gray-600 leading-relaxed text-lg">
                    {property.description}
                </p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    {#each property.features || [] as feature}
                        <div class="bg-gray-50 p-4 flex flex-col items-center text-center gap-2">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Feature</span>
                            <span class="font-bold text-sm">{feature}</span>
                        </div>
                    {/each}
                </div>
            </div>

            {#if videos.length > 0}
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold flex items-center gap-2">
                        <span class="material-symbols-outlined text-brand-action">videocam</span>
                        Virtual Tours
                    </h2>
                    <div class="grid grid-cols-1 gap-6">
                        {#each videos as video}
                            <div class="bg-black rounded-2xl overflow-hidden shadow-lg aspect-video border border-brand-primary/10">
                                <video 
                                    src="/storage/{video.path}" 
                                    controls 
                                    class="w-full h-full"
                                    preload="metadata"
                                >
                                    <track kind="captions" />
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        {/each}
                    </div>
                </div>
            {/if}
        </div>

        <div class="space-y-8">
            <div class="bg-brand-action p-8 text-white shadow-xl shadow-brand-action/20 space-y-6 sticky top-28 rounded-xl">
                <div class="space-y-1">
                    <p class="text-white/60 text-sm font-black uppercase tracking-widest">Monthly Rent</p>
                    <div class="flex items-baseline gap-1">
                        <span class="text-5xl font-black">${property.price.toLocaleString()}</span>
                        <span class="text-white/60 font-bold">/mo</span>
                    </div>
                </div>

                <div class="space-y-4 pt-6 border-t border-white/10">
                    <h3 class="font-bold text-lg flex items-center gap-2">
                        <span class="material-symbols-outlined">gavel</span>
                        Legal Compliance
                    </h3>
                    <div class="space-y-3">
                        {#each Object.entries(property.compliance || {}) as [cert, status]}
                            <div class="flex justify-between items-center bg-white/10 p-3 rounded-lg">
                                <span class="text-sm font-bold capitalize">{cert} Certificate</span>
                                <Badge type={status === 'Verified' ? 'success' : status === 'Pending' ? 'warning' : 'error'}>
                                    {status}
                                </Badge>
                            </div>
                        {/each}
                    </div>
                </div>

                <Button variant="secondary" size="xl" class="w-full bg-white text-brand-action hover:bg-brand-bg border-none" onclick={onScheduleVisit}>
                    Schedule a Visit
                </Button>
                <p class="text-center text-[10px] text-white/60 font-bold uppercase tracking-widest">Secure through Keystone Legal Framework</p>
            </div>
        </div>
    </div>
</div>
