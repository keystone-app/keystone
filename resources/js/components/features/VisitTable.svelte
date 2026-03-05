<script>
    import { Calendar, Eye } from 'lucide-svelte';
    import Badge from '../ui/Badge.svelte';
    import Button from '../ui/Button.svelte';
    import EmptyState from '../ui/EmptyState.svelte';

    let { 
        visits, 
        role = 'tenant', 
        onApprove, 
        onReject, 
        onViewId 
    } = $props();

    const statusTypes = {
        pending: 'warning',
        scheduled: 'success',
        rejected: 'error',
        cancelled: 'info'
    };
</script>

<div class="bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm">
    {#if visits.length > 0}
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    {#if role === 'landlord'}
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Tenant</th>
                    {/if}
                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Property</th>
                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Date & Time</th>
                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Status</th>
                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                {#each visits as visit}
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        {#if role === 'landlord'}
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="font-bold text-sm">{visit.user.name}</span>
                                    <button 
                                        class="flex items-center gap-1 text-[10px] text-brand-action font-black uppercase tracking-widest hover:underline mt-0.5"
                                        onclick={() => onViewId(visit)}
                                    >
                                        <Eye size={10} />
                                        View ID Document
                                    </button>
                                </div>
                            </td>
                        {/if}
                        <td class="px-6 py-5 text-sm text-gray-600 font-bold">{visit.property.name}</td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2 text-brand-primary font-bold text-sm">
                                <Calendar size={14} class="text-brand-action" />
                                {new Date(visit.visit_at).toLocaleString()}
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <Badge type={statusTypes[visit.status]}>
                                {visit.status}
                            </Badge>
                        </td>
                        <td class="px-6 py-5 text-right">
                            {#if role === 'landlord' && visit.status === 'pending'}
                                <div class="flex justify-end gap-2">
                                    <Button variant="primary" size="sm" onclick={() => onApprove(visit.id)} class="text-[10px] font-black uppercase px-3 py-1.5">Approve</Button>
                                    <Button variant="secondary" size="sm" onclick={() => onReject(visit.id)} class="text-[10px] font-black uppercase px-3 py-1.5 text-red-600 border-red-100 hover:bg-red-50">Reject</Button>
                                </div>
                            {:else if role === 'tenant' && visit.status === 'pending'}
                                <Button variant="secondary" size="sm" class="text-[10px] font-black uppercase px-3 py-1.5">Cancel</Button>
                            {:else}
                                <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">No actions</span>
                            {/if}
                        </td>
                    </tr>
                {/each}
            </tbody>
        </table>
    {:else}
        <EmptyState 
            icon="event_busy"
            title="No visits found"
            message="You don't have any scheduled or pending visits at the moment."
        />
    {/if}
</div>
