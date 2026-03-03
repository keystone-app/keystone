<script>
    import { Home, Calendar, Clock, CheckCircle2, FileText } from 'lucide-svelte';
    import Badge from '../ui/Badge.svelte';
    import Button from '../ui/Button.svelte';

    let { 
        visits, 
        role = 'tenant', 
        onApprove, 
        onReject, 
        onMakeOffer,
        onViewId
    } = $props();

    const statusTypes = {
        pending: 'warning',
        scheduled: 'success',
        rejected: 'error',
        cancelled: 'default'
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
                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Schedule</th>
                {#if role === 'landlord'}
                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Identity</th>
                {:else}
                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">ID Verification</th>
                {/if}
                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Status</th>
                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            {#each visits as visit}
                <tr class="hover:bg-gray-50/50 transition-colors">
                    {#if role === 'landlord'}
                        <td class="px-6 py-5 font-bold text-sm">{visit.user.name}</td>
                    {/if}
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                                <Home size={20} />
                            </div>
                            <span class="font-bold text-sm">{visit.property.name}</span>
                        </div>
                    </td>
                    <td class="px-6 py-5 text-sm">
                        <div class="flex flex-col">
                            <span class="font-bold">{new Date(visit.visit_at).toLocaleDateString()}</span>
                            <span class="text-xs text-gray-400 font-medium">{new Date(visit.visit_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        {#if role === 'landlord'}
                            <button 
                                class="text-indigo-600 hover:text-indigo-800 text-xs font-bold flex items-center gap-1 group"
                                onclick={() => onViewId(visit)}
                            >
                                <FileText size={14} class="text-indigo-400 group-hover:text-indigo-600" />
                                View ID
                            </button>
                        {:else}
                            <div class="flex items-center gap-2 text-xs text-green-600 font-bold">
                                <CheckCircle2 size={14} />
                                Verified
                            </div>
                        {/if}
                    </td>
                    <td class="px-6 py-5">
                        <Badge type={statusTypes[visit.status]}>
                            {visit.status}
                        </Badge>
                    </td>
                    <td class="px-6 py-5 text-right">
                        {#if role === 'landlord'}
                            {#if visit.status === 'pending'}
                                <div class="flex justify-end gap-2">
                                    <Button variant="primary" size="sm" onclick={() => onApprove(visit.id)} class="text-[10px] uppercase px-3 py-1.5">
                                        Approve
                                    </Button>
                                    <Button variant="secondary" size="sm" onclick={() => onReject(visit.id)} class="text-[10px] uppercase px-3 py-1.5">
                                        Reject
                                    </Button>
                                </div>
                            {:else}
                                <span class="text-xs text-gray-400 font-bold italic">Resolved</span>
                            {/if}
                        {:else}
                            {#if visit.status === 'scheduled'}
                                <Button variant="primary" size="sm" onclick={() => onMakeOffer(visit)} class="text-[10px] uppercase px-4 py-2">
                                    Make Offer
                                </Button>
                            {:else}
                                <span class="text-xs text-gray-400 font-bold italic">Wait for approval</span>
                            {/if}
                        {/if}
                    </td>
                </tr>
            {/each}
        </tbody>
    </table>
    {#if visits.length === 0}
        <div class="p-20 text-center space-y-4">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto text-gray-300">
                <Calendar size={32} />
            </div>
            <p class="text-gray-500 font-medium">No visits found.</p>
        </div>
    {/if}
</div>
