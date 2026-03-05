<script>
    import { FileText, CheckCircle2 } from 'lucide-svelte';
    import Badge from '../ui/Badge.svelte';
    import Button from '../ui/Button.svelte';
    import EmptyState from '../ui/EmptyState.svelte';

    let { 
        offers, 
        role = 'tenant', 
        onUpdateStatus,
        onUploadCompliance,
        onVerifyIncome
    } = $props();

    const statusTypes = {
        pending: 'warning',
        accepted: 'success',
        rejected: 'error',
        countered: 'info'
    };
</script>

<div class="bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm">
    {#if offers.length > 0}
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    {#if role === 'landlord'}
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Tenant</th>
                    {/if}
                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Property</th>
                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Offer Amount</th>
                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Compliance</th>
                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Status</th>
                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                {#each offers as offer}
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        {#if role === 'landlord'}
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="font-bold text-sm">{offer.user.name}</span>
                                    <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Verified Identity</span>
                                </div>
                            </td>
                        {/if}
                        <td class="px-6 py-5 text-sm text-gray-600 font-bold">{offer.property.name}</td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col">
                                <span class="text-brand-action font-black">${parseFloat(offer.amount).toLocaleString()}/mo</span>
                                <span class="text-[10px] text-gray-400 truncate max-w-[150px] font-bold uppercase tracking-wider">"{offer.terms}"</span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            {#if offer.compliance_status_label === 'none'}
                                <span class="text-[10px] text-gray-300 font-black uppercase">N/A</span>
                            {:else if offer.compliance_status_label === 'awaiting_documents'}
                                <div class="flex items-center gap-2 text-[10px] font-black uppercase text-amber-600">
                                    <div class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></div>
                                    Uploading Docs
                                </div>
                            {:else if offer.compliance_status_label === 'pending_verification'}
                                <div class="flex items-center gap-2 text-[10px] font-black uppercase text-indigo-600">
                                    <div class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-pulse"></div>
                                    In Verification
                                </div>
                            {:else if offer.compliance_status_label === 'verified'}
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-1.5 text-[10px] font-black uppercase text-green-600">
                                        <CheckCircle2 size={12} />
                                        Income Verified
                                    </div>
                                </div>
                            {/if}
                        </td>
                        <td class="px-6 py-5">
                            <Badge type={statusTypes[offer.status]}>
                                {offer.status}
                            </Badge>
                        </td>
                        <td class="px-6 py-5 text-right">
                            {#if role === 'landlord'}
                                {#if offer.status === 'pending'}
                                    <div class="flex justify-end gap-2">
                                        <Button variant="primary" size="sm" onclick={() => onUpdateStatus(offer.id, 'accepted')} class="text-[10px] font-black uppercase px-3 py-1.5">Accept</Button>
                                        <Button variant="secondary" size="sm" onclick={() => onUpdateStatus(offer.id, 'countered')} class="text-[10px] font-black uppercase px-3 py-1.5">Counter</Button>
                                        <Button variant="secondary" size="sm" onclick={() => onUpdateStatus(offer.id, 'rejected')} class="text-[10px] font-black uppercase px-3 py-1.5 text-red-600 border-red-100 hover:bg-red-50">Reject</Button>
                                    </div>
                                {:else}
                                    <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Negotiation Ended</span>
                                {/if}
                            {:else}
                                {#if offer.status === 'accepted'}
                                    {#if offer.compliance_status_label === 'awaiting_documents'}
                                        <div class="flex justify-end gap-2">
                                            <Button variant="outline" size="sm" class="text-[10px] font-black uppercase relative">
                                                Income
                                                <input type="file" class="absolute inset-0 opacity-0 cursor-pointer" onchange={(e) => onUploadCompliance(offer.id, 'income_proof', e)} />
                                            </Button>
                                            <Button variant="outline" size="sm" class="text-[10px] font-black uppercase relative">
                                                Residency
                                                <input type="file" class="absolute inset-0 opacity-0 cursor-pointer" onchange={(e) => onUploadCompliance(offer.id, 'residency_proof', e)} />
                                            </Button>
                                        </div>
                                    {:else if offer.compliance_status_label === 'pending_verification'}
                                        <Button variant="primary" size="sm" onclick={() => onVerifyIncome(offer.id)} class="text-[10px] font-black uppercase">
                                            Verify Identity
                                        </Button>
                                    {:else if offer.compliance_status_label === 'verified'}
                                        <span class="text-[10px] text-brand-action font-black uppercase tracking-widest flex items-center justify-end gap-1">
                                            <FileText size={14} />
                                            Ready for Lease
                                        </span>
                                    {/if}
                                {:else if offer.status === 'pending'}
                                    <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Awaiting Response</span>
                                {/if}
                            {/if}
                        </td>
                    </tr>
                {/each}
            </tbody>
        </table>
    {:else}
        <EmptyState 
            icon="description"
            title="No offers found"
            message="There are currently no active offers to display."
        />
    {/if}
</div>
