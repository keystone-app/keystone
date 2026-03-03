<script>
    import { Plus } from 'lucide-svelte';
    import Modal from '../ui/Modal.svelte';
    import Button from '../ui/Button.svelte';

    let { 
        isOpen, 
        onClose, 
        property, 
        onSubmit,
        isSubmitting = false
    } = $props();

    let amount = $state(0);
    let terms = $state('Standard legal terms as per Keystone framework.');

    $effect(() => {
        if (isOpen && property) {
            amount = property.price;
        }
    });

    async function handleSubmit() {
        if (await onSubmit(amount, terms)) {
            onClose();
        }
    }
</script>

<Modal {isOpen} {onClose} title="Make an Offer" maxWidth="max-w-md">
    <div class="space-y-8">
        <div class="text-center space-y-2">
            <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto text-white shadow-lg shadow-indigo-200 mb-6">
                <Plus size={32} />
            </div>
            <h2 class="text-3xl font-black">Make an Offer</h2>
            <p class="text-gray-500">Propose your terms for {property?.name}</p>
        </div>

        <form class="space-y-4" onsubmit={handleSubmit}>
            <div class="space-y-1">
                <label for="offer-amount" class="text-xs font-black uppercase tracking-widest text-gray-400">Monthly Rent ($)</label>
                <input 
                    id="offer-amount"
                    type="number" 
                    bind:value={amount}
                    placeholder="2500"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold text-xl text-indigo-600"
                    required
                />
            </div>
            <div class="space-y-1">
                <label for="offer-terms" class="text-xs font-black uppercase tracking-widest text-gray-400">Additional Terms</label>
                <textarea 
                    id="offer-terms"
                    bind:value={terms}
                    placeholder="Any special requests or conditions..."
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-medium h-32"
                ></textarea>
            </div>
            <Button 
                type="submit"
                variant="primary"
                size="xl"
                class="w-full"
                disabled={isSubmitting}
            >
                {#if isSubmitting}
                    Signing in...
                {:else}
                    Send Offer
                {/if}
            </Button>
        </form>
    </div>
</Modal>
