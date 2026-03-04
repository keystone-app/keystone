<script>
    import { Plus } from 'lucide-svelte';
    import Button from '../ui/Button.svelte';
    import Badge from '../ui/Badge.svelte';
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
                <p class="text-gray-500 mt-1">
                    {#if landlordView === 'properties'}Manage your real estate portfolio
                    {:else if landlordView === 'visits'}Review and approve tenant visits
                    {:else}Manage deal closures and compliance
                    {/if}
                </p>
            </div>
            {#if landlordView === 'properties'}
                <Button variant="primary" size="lg" onclick={onAddProperty}>
                    <Plus size={20} />
                    Add Property
                </Button>
            {/if}
        </div>
    </header>

    {#if landlordView === 'properties'}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {#each properties.filter(p => p.id <= 3) as property}
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    <div class="h-48 bg-gray-50 flex items-center justify-center border-b border-gray-50">
                        <span class="material-symbols-outlined text-gray-200 text-5xl">home</span>
                    </div>
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-lg text-brand-primary">{property.name}</h3>
                            <Badge type="success">Occupied</Badge>
                        </div>
                        <p class="text-gray-500 text-sm mb-4">{property.address}</p>
                        <div class="flex justify-between items-center pt-4 border-t border-gray-50">
                            <span class="font-black text-brand-action">${property.price.toLocaleString()}/mo</span>
                            <Button variant="ghost" class="text-brand-action text-xs font-bold">Manage</Button>
                        </div>
                    </div>
                </div>
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
