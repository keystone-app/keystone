<script>
    import { Home } from 'lucide-svelte';
    import Badge from '../ui/Badge.svelte';
    import Button from '../ui/Button.svelte';

    let { property, onBack, onScheduleVisit } = $props();
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
            <div class="h-[400px] bg-white rounded-xl flex items-center justify-center border border-gray-200 shadow-sm overflow-hidden">
                <span class="material-symbols-outlined text-gray-100 text-9xl">image</span>
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
