<script>
    import { Plus } from 'lucide-svelte';
    import Button from '../ui/Button.svelte';
    import Badge from '../ui/Badge.svelte';
    import Card from '../ui/Card.svelte';
    import EmptyState from '../ui/EmptyState.svelte';
    import PriceDisplay from '../ui/PriceDisplay.svelte';
    import VisitTable from './VisitTable.svelte';
    import OfferTable from './OfferTable.svelte';

    let { 
        landlordView, 
        properties, 
        landlordVisits, 
        offers, 
        maintenanceRequests = [],
        onAddProperty,
        onApproveVisit,
        onRejectVisit,
        onUpdateOfferStatus,
        onUpdateMaintenanceStatus
    } = $props();
</script>

<div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
    <header class="flex flex-col gap-6">
        <div class="flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-black text-brand-primary">
                    {landlordView === 'properties' ? 'Properties' : landlordView === 'visits' ? 'Visit Requests' : landlordView === 'maintenance' ? 'Maintenance Management' : 'Offer Negotiations'}
                </h1>
                <p class="text-gray-500 mt-1 font-medium">
                    {#if landlordView === 'properties'}Manage your real estate portfolio
                    {:else if landlordView === 'visits'}Review and approve tenant visits
                    {:else if landlordView === 'maintenance'}Track and resolve property issues
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
        {#if properties.length > 0}
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
        {:else}
            <EmptyState 
                icon="home"
                title="No properties found"
                message="Start building your portfolio by listing your first property."
                actionLabel="Add Property"
                onAction={onAddProperty}
            />
        {/if}
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
    {:else if landlordView === 'maintenance'}
        {#if maintenanceRequests.length > 0}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Date</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Property</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Issue</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        {#each maintenanceRequests as request}
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-gray-500">
                                    {new Date(request.created_at).toLocaleDateString()}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm font-bold text-brand-primary">{request.lease?.property?.name}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-brand-primary">{request.title}</p>
                                    {#if request.description}
                                        <p class="text-xs text-gray-400 line-clamp-1">{request.description}</p>
                                    {/if}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <Badge type={
                                        request.status === 'reported' ? 'warning' :
                                        request.status === 'in_progress' ? 'info' :
                                        request.status === 'resolved' ? 'success' : 'neutral'
                                    }>
                                        <span class="capitalize">{request.status.replace('_', ' ')}</span>
                                    </Badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    {#if request.status === 'reported'}
                                        <Button variant="secondary" size="xs" onclick={() => onUpdateMaintenanceStatus(request.id, 'in_progress')}>
                                            Start Work
                                        </Button>
                                    {:else if request.status === 'in_progress'}
                                        <Button variant="primary" size="xs" class="bg-green-600 hover:bg-green-700" onclick={() => onUpdateMaintenanceStatus(request.id, 'resolved')}>
                                            Mark Resolved
                                        </Button>
                                    {/if}
                                </td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>
        {:else}
            <EmptyState 
                icon="handyman"
                title="No maintenance requests"
                message="Maintenance requests for your properties will appear here."
            />
        {/if}
    {/if}
</div>
