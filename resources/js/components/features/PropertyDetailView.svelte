<script>
    import { Home } from 'lucide-svelte';
    import Badge from '../ui/Badge.svelte';
    import Button from '../ui/Button.svelte';
    import PriceDisplay from '../ui/PriceDisplay.svelte';
    import MediaGallery from '../ui/MediaGallery.svelte';

    let { property, onBack, onScheduleVisit } = $props();

    const videos = $derived(property.media?.filter(m => m.type === 'property_video') || []);
</script>

<div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
    <button 
        class="flex items-center gap-2 text-brand-primary/50 hover:text-brand-action font-black uppercase tracking-widest text-[10px] transition-colors mb-4"
        onclick={onBack}
    >
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        Back to Listings
    </button>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <div class="lg:col-span-2 space-y-8">
            <MediaGallery media={property.media || []} />
            
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
                    <PriceDisplay price={property.price} size="xl" currency="$" suffix="/mo" class="text-white" />
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

                <Button variant="secondary" size="xl" class="w-full bg-white text-brand-action hover:bg-brand-bg border-none font-black uppercase tracking-widest" onclick={onScheduleVisit}>
                    Schedule a Visit
                </Button>
                <p class="text-center text-[10px] text-white/60 font-bold uppercase tracking-widest">Secure through Keystone Legal Framework</p>
            </div>
        </div>
    </div>
</div>
