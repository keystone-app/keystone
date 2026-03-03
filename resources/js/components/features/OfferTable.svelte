<script>
    import { FileText, CheckCircle2 } from 'lucide-svelte';
    import Badge from '../ui/Badge.svelte';
    import Button from '../ui/Button.svelte';

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
                    <td class="px-6 py-5 text-sm text-gray-600">{offer.property.name}</td>
                    <td class="px-6 py-5">
                        <div class="flex flex-col">
                            <span class="text-indigo-600 font-black">${parseFloat(offer.amount).toLocaleString()}/mo</span>
                            <span class="text-[10px] text-gray-400 truncate max-w-[150px] italic">"{offer.terms}"</span>
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        {#if offer.compliance_status === 'none'}
                            <span class="text-xs text-gray-300 font-bold italic">N/A</span>
                        {:else if offer.compliance_status === 'awaiting_documents'}
                            <div class="flex items-center gap-2 text-[10px] font-black uppercase text-amber-600">
                                <div class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></div>
                                Uploading Docs
                            </div>
                        {:else if offer.compliance_status === 'pending_verification'}
                            <div class="flex items-center gap-2 text-[10px] font-black uppercase text-indigo-600">
                                <div class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-pulse"></div>
                                Pending Open Finance
                            </div>
                        {:else if offer.compliance_status === 'verified'}
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-1.5 text-[10px] font-black uppercase text-green-600">
                                    <CheckCircle2 size={12} />
                                    Income Verified
                                </div>
                                <span class="text-[10px] font-bold text-green-800 bg-green-50 px-1.5 py-0.5 rounded-md">
                                    Verified via Open Finance
                                </span>
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
                                    <Button variant="primary" size="sm" onclick={() => onUpdateStatus(offer.id, 'accepted')} class="text-[10px] uppercase px-3 py-1.5">Accept</Button>
                                    <Button variant="secondary" size="sm" onclick={() => onUpdateStatus(offer.id, 'countered')} class="text-[10px] uppercase px-3 py-1.5">Counter</Button>
                                    <Button variant="secondary" size="sm" onclick={() => onUpdateStatus(offer.id, 'rejected')} class="text-[10px] uppercase px-3 py-1.5 text-red-600 border-red-100 hover:bg-red-50">Reject</Button>
                                </div>
                            {:else}
                                <span class="text-xs text-gray-400 font-bold italic">Negotiation Ended</span>
                            {/if}
                        {:else}
                            {#if offer.status === 'accepted'}
                                {#if offer.compliance_status === 'awaiting_documents'}
                                    <div class="flex justify-end gap-2">
                                        <Button variant="outline" size="sm" class="text-[10px] relative">
                                            Income Proof
                                            <input type="file" class="absolute inset-0 opacity-0 cursor-pointer" onchange={(e) => onUploadCompliance(offer.id, 'income_proof', e)} />
                                        </Button>
                                        <Button variant="outline" size="sm" class="text-[10px] relative">
                                            Residency
                                            <input type="file" class="absolute inset-0 opacity-0 cursor-pointer" onchange={(e) => onUploadCompliance(offer.id, 'residency_proof', e)} />
                                        </Button>
                                    </div>
                                {:else if offer.compliance_status === 'pending_verification'}
                                    <Button variant="primary" size="sm" onclick={() => onVerifyIncome(offer.id)} class="text-[10px] uppercase">
                                        Verify with Open Finance
                                    </Button>
                                {:else if offer.compliance_status === 'verified'}
                                    <span class="text-xs text-indigo-600 font-bold flex items-center justify-end gap-1">
                                        <FileText size={14} />
                                        Ready for Lease
                                    </span>
                                {/if}
                            {:else if offer.status === 'pending'}
                                <span class="text-xs text-gray-400 font-bold italic">Awaiting Response</span>
                            {/if}
                        {/if}
                    </td>
                </tr>
            {/each}
        </tbody>
    </table>
    {#if offers.length === 0}
        <div class="p-20 text-center space-y-4">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto text-gray-300">
                <FileText size={32} />
            </div>
            <p class="text-gray-500 font-medium">No offers found.</p>
        </div>
    {/if}
</div>
