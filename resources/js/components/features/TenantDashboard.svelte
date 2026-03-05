<script>
    import { CheckCircle2, FileText, Plus } from 'lucide-svelte';
    import Button from '../ui/Button.svelte';
    import Badge from '../ui/Badge.svelte';
    import EmptyState from '../ui/EmptyState.svelte';
    import VisitTable from './VisitTable.svelte';
    import OfferTable from './OfferTable.svelte';

    let { 
        tenantView, 
        myVisits, 
        offers, 
        identityDoc,
        maintenanceRequests = [],
        myLeases = [],
        onMakeOffer,
        onReportMaintenance,
        onUploadCompliance,
        onVerifyIncome
    } = $props();
</script>

<div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
    <header class="flex flex-col gap-6">
        <div class="space-y-1">
            <h1 class="text-3xl font-black text-brand-primary">
                {#if tenantView === 'visits'}My Scheduled Visits
                {:else if tenantView === 'offers'}Negotiations
                {:else if tenantView === 'payments'}Payments
                {:else if tenantView === 'maintenance'}Maintenance Requests
                {:else}My Leases
                {/if}
            </h1>
            <p class="text-gray-500 mt-1">
                {#if tenantView === 'visits'}Track and manage your upcoming property visits
                {:else if tenantView === 'offers'}Negotiate and track your property offers
                {:else if tenantView === 'payments'}View your payment history and upcoming rent
                {:else if tenantView === 'maintenance'}Report and track issues with your property
                {:else}Manage your active lease agreements
                {/if}
            </p>
        </div>
    </header>

    {#if tenantView === 'leases'}
        {#if myLeases.length > 0}
            <div class="space-y-6">
                {#each myLeases as lease}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-brand-action/10 rounded-lg flex items-center justify-center text-brand-action">
                                    <span class="material-symbols-outlined">description</span>
                                </div>
                                <div>
                                    <h2 class="font-bold text-lg text-brand-primary">Active Lease: {lease.property?.name}</h2>
                                    <p class="text-gray-500 text-sm font-medium">Rent: ${lease.rent_amount}</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <Button variant="secondary" size="sm" onclick={() => onReportMaintenance(lease)}>
                                    <span class="material-symbols-outlined mr-2">handyman</span>
                                    Report Issue
                                </Button>
                            </div>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div>
                                <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-2">Status</p>
                                <div class="flex items-center gap-2">
                                    <div class="w-2.5 h-2.5 bg-brand-success rounded-full animate-pulse"></div>
                                    <span class="font-bold capitalize">{lease.status_name || 'Active'}</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-2">Property Address</p>
                                <span class="font-bold text-sm">{lease.property?.address}</span>
                            </div>
                            <div>
                                <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-2">Landlord</p>
                                <span class="font-bold">{lease.landlord?.name}</span>
                            </div>
                        </div>
                    </div>
                {/each}
            </div>
        {:else}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-brand-action/10 rounded-lg flex items-center justify-center text-brand-action">
                            <span class="material-symbols-outlined">description</span>
                        </div>
                        <div>
                            <h2 class="font-bold text-lg text-brand-primary">No Active Leases</h2>
                            <p class="text-gray-500 text-sm font-medium">Search for properties to start a lease.</p>
                        </div>
                    </div>
                </div>
            </div>
        {/if}

        <section class="space-y-4">
            <h2 class="text-xl font-bold text-brand-primary">Documents</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border-2 border-dashed border-gray-200 rounded-2xl p-8 flex flex-col items-center justify-center text-center hover:border-brand-action/30 transition-colors cursor-pointer group bg-white/50">
                    <div class="w-12 h-12 bg-brand-primary/5 rounded-full flex items-center justify-center mb-4 group-hover:bg-brand-action/10 transition-colors text-brand-primary/30 group-hover:text-brand-action">
                        <Plus size={24} />
                    </div>
                    <h3 class="font-bold text-brand-primary">Upload Document</h3>
                    <p class="text-brand-primary/50 text-sm mt-1">Insurance, ID, or income verification</p>
                </div>
                <div class="bg-white border border-brand-primary/5 rounded-2xl p-6 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-brand-action/10 rounded-lg flex items-center justify-center text-brand-action">
                            <FileText size={20} />
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-brand-primary">Income_Verification.pdf</h4>
                            <p class="text-brand-primary/30 text-[10px] font-bold mt-0.5">Uploaded 2 days ago</p>
                        </div>
                    </div>
                    <Badge type="success">Verified</Badge>
                </div>
                {#if identityDoc}
                    <div class="bg-white border border-brand-action/20 rounded-2xl p-6 flex items-center justify-between shadow-sm border-l-4 border-l-brand-action">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-brand-action/10 rounded-lg flex items-center justify-center text-brand-action">
                                <CheckCircle2 size={20} />
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-brand-primary">{identityDoc.name}</h4>
                                <p class="text-brand-primary/30 text-[10px] font-bold mt-0.5">Primary Identity Document</p>
                            </div>
                        </div>
                        <Badge type="info">Verified</Badge>
                    </div>
                {/if}
            </div>
        </section>
    {:else if tenantView === 'visits'}
        <VisitTable visits={myVisits} onMakeOffer={onMakeOffer} />
    {:else if tenantView === 'offers'}
        <OfferTable 
            offers={offers} 
            onUploadCompliance={onUploadCompliance} 
            onVerifyIncome={onVerifyIncome} 
        />
    {:else if tenantView === 'payments'}
        <EmptyState 
            icon="payments"
            title="No payments found"
            message="Your payment history will appear here once you have an active lease."
        />
    {:else if tenantView === 'maintenance'}
        {#if maintenanceRequests.length > 0}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Date</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Property</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Issue</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Status</th>
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
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>
        {:else}
            <EmptyState 
                icon="handyman"
                title="No maintenance requests"
                message="Report and track property issues here."
                actionLabel={myLeases.length > 0 ? "Report an Issue" : null}
                onAction={() => {
                    if (myLeases.length > 0) onReportMaintenance(myLeases[0]);
                }}
            />
        {/if}
    {/if}
</div>
