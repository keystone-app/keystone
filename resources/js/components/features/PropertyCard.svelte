<script>
    import { createRawSnippet } from 'svelte';
    import Card from '../ui/Card.svelte';
    import Button from '../ui/Button.svelte';
    import Badge from '../ui/Badge.svelte';
    import PriceDisplay from '../ui/PriceDisplay.svelte';

    let { property, onViewDetails } = $props();

    const statusTypes = {
        available: 'success',
        rented: 'info',
        maintenance: 'warning'
    };

    const thumbnail = $derived(property.media?.find(m => m.type === 'property_image')?.path);

    const header = createRawSnippet(() => ({
        render: () => `
            <div class="flex justify-between items-center w-full">
                <span class="bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm">
                    ${property.type}
                </span>
                <div class="badge-container"></div>
            </div>
        `
    }));
</script>

<!-- Note: Svelte 5 snippets are better used within the same file or passed as props. 
     Using a more standard approach for Card content below -->

<Card class="group h-full flex flex-col" padding="p-0">
    <div class="h-56 bg-gray-100 flex items-center justify-center border-b border-gray-100 relative overflow-hidden">
        {#if thumbnail}
            <img src="/storage/{thumbnail}" alt={property.name} class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
        {:else}
            <span class="material-symbols-outlined text-gray-200 text-6xl group-hover:text-indigo-200 transition-colors">home</span>
        {/if}
        <div class="absolute top-4 left-4">
            <span class="bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm">
                {property.type}
            </span>
        </div>
        <div class="absolute top-4 right-4">
            <Badge type={statusTypes[property.status]}>
                {property.status}
            </Badge>
        </div>
    </div>
    <div class="p-6 flex-1 flex flex-col">
        <h3 class="font-extrabold text-xl mb-1 group-hover:text-brand-action transition-colors line-clamp-1">{property.name}</h3>
        <p class="text-gray-500 text-xs mb-6 flex items-center gap-1 font-bold uppercase tracking-wider">
            <span class="material-symbols-outlined text-sm text-brand-action">location_on</span>
            {property.address}
        </p>
        
        <div class="mt-auto pt-5 border-t border-gray-100 flex justify-between items-center">
            <PriceDisplay price={property.price} size="md" />
            <Button variant="outline" size="sm" onclick={() => onViewDetails(property)} class="font-bold text-xs uppercase tracking-widest">
                Details
            </Button>
        </div>
    </div>
</Card>
