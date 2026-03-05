<script>
    import { Wrench } from 'lucide-svelte';
    import Modal from '../ui/Modal.svelte';
    import Button from '../ui/Button.svelte';

    let { 
        isOpen, 
        onClose, 
        lease, 
        onSubmit,
        isSubmitting = false
    } = $props();

    let title = $state('');
    let description = $state('');

    $effect(() => {
        if (!isOpen) {
            title = '';
            description = '';
        }
    });

    async function handleSubmit(e) {
        e.preventDefault();
        if (!title) return;
        
        const success = await onSubmit({
            lease_id: lease.id,
            title,
            description
        });
        
        if (success) {
            onClose();
        }
    }
</script>

<Modal {isOpen} {onClose} title="Report Maintenance Issue" maxWidth="max-w-md">
    <div class="space-y-8">
        <div class="text-center space-y-2">
            <div class="w-16 h-16 bg-orange-500 rounded-2xl flex items-center justify-center mx-auto text-white shadow-lg shadow-orange-100 mb-6">
                <Wrench size={32} />
            </div>
            <h2 class="text-3xl font-black">Report Issue</h2>
            <p class="text-gray-500">For {lease?.property?.name}</p>
        </div>

        <form class="space-y-4" onsubmit={handleSubmit}>
            <div class="space-y-1">
                <label for="issue-title" class="text-xs font-black uppercase tracking-widest text-gray-400">Issue Title</label>
                <input 
                    id="issue-title"
                    type="text" 
                    bind:value={title}
                    placeholder="e.g., Leaking kitchen tap"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none font-bold"
                    required
                />
            </div>
            <div class="space-y-1">
                <label for="issue-description" class="text-xs font-black uppercase tracking-widest text-gray-400">Description</label>
                <textarea 
                    id="issue-description"
                    bind:value={description}
                    placeholder="Please provide details about the issue..."
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none font-medium h-32"
                ></textarea>
            </div>
            <Button 
                type="submit"
                variant="primary"
                size="xl"
                class="w-full bg-orange-600 hover:bg-orange-700 shadow-orange-100"
                disabled={isSubmitting}
            >
                {#if isSubmitting}
                    Submitting...
                {:else}
                    Submit Request
                {/if}
            </Button>
        </form>
    </div>
</Modal>
