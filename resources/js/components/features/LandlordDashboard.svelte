<script>
    import { Plus } from 'lucide-svelte';
    import Button from '../ui/Button.svelte';
    import Badge from '../ui/Badge.svelte';
    import Card from '../ui/Card.svelte';
    import PriceDisplay from '../ui/PriceDisplay.svelte';
    import VisitTable from './VisitTable.svelte';
    import OfferTable from './OfferTable.svelte';

    let { 
        landlordView, 
        properties, 
        landlordVisits, 
        offers, 
        onAddProperty,
        onApproveVisit,
        onRejectVisit,
        onUpdateOfferStatus
    } = $props();
</script>

<div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
    <header class="flex flex-col gap-6">
        <div class="flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-black text-brand-primary">
                    {landlordView === 'properties' ? 'Properties' : landlordView === 'visits' ? 'Visit Requests' : 'Offer Negotiations'}
                </h1>
                <p class="text-gray-500 mt-1 font-medium">
                    {#if landlordView === 'properties'}Manage your real estate portfolio
                    {:else if landlordView === 'visits'}Review and approve tenant visits
                    {:else}Manage deal closures and compliance
                    {/if}
                </p>
            </div>
            {#if landlordView === 'properties'}
                <Button variant="primary" size="lg" onclick={onAddProperty} class="font-black uppercase tracking-widest text-xs">
                    <Plus size={20} />
                    Add Property
                </Button>
            {/if}
        </div>
    </header>

    {#if landlordView === 'properties'}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {#each properties as property}
                {@const thumbnail = property.media?.find(m => m.type === 'property_image')?.path}
                <Card padding="p-0">
                    <div class="h-48 bg-gray-50 flex items-center justify-center border-b border-gray-50 overflow-hidden relative">
                        {#if thumbnail}
                            <img src="/storage/{thumbnail}" alt={property.name} class="w-full h-full object-cover" />
                        {:else}
                            <span class="material-symbols-outlined text-gray-200 text-5xl">home</span>
                        {/if}
                        <div class="absolute top-4 right-4">
                            <Badge type={property.status === 'available' ? 'success' : 'info'}>{property.status}</Badge>
                        </div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg text-brand-primary line-clamp-1 mb-1">{property.name}</h3>
                        <p class="text-gray-500 text-xs mb-4 font-medium">{property.address}</p>
                        <div class="flex justify-between items-center pt-4 border-t border-gray-50">
                            <PriceDisplay price={property.price} size="sm" />
                            <Button variant="ghost" class="text-brand-action text-[10px] font-black uppercase tracking-widest px-3">Manage</Button>
                        </div>
                    </div>
                </Card>
            {/each}
        </div>
    {:else if landlordView === 'visits'}
        <VisitTable 
            visits={landlordVisits} 
            role="landlord" 
            onApprove={onApproveVisit} 
            onReject={onRejectVisit} 
            onViewId={(v) => alert('Viewing ID: ' + v.document.name)} 
        />
    {:else if landlordView === 'offers'}
        <OfferTable 
            offers={offers} 
            role="landlord" 
            onUpdateStatus={onUpdateOfferStatus} 
        />
    {/if}
</div>
