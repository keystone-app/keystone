<script>
    import Modal from '../ui/Modal.svelte';
    import Button from '../ui/Button.svelte';
    import { Plus, X } from 'lucide-svelte';

    let { 
        isOpen, 
        onClose, 
        onSubmit,
        isSubmitting = false
    } = $props();

    let name = $state('');
    let address = $state('');
    let price = $state('');
    let type = $state('Apartment');
    let description = $state('');

    async function handleSubmit() {
        const success = await onSubmit({
            name,
            address,
            price: parseFloat(price),
            type,
            description
        });

        if (success) {
            resetForm();
            onClose();
        }
    }

    function resetForm() {
        name = '';
        address = '';
        price = '';
        type = 'Apartment';
        description = '';
    }
</script>

<Modal {isOpen} {onClose} title="Add New Property" maxWidth="max-w-lg">
    <form class="space-y-6" onsubmit={handleSubmit}>
        <div class="space-y-1">
            <label for="prop-name" class="text-xs font-black uppercase tracking-widest text-gray-400">Property Name</label>
            <input 
                id="prop-name"
                type="text" 
                bind:value={name}
                placeholder="Modern Apartment 101"
                class="w-full px-4 py-3 bg-brand-primary/5 border border-brand-primary/10 rounded-xl focus:ring-2 focus:ring-brand-action outline-none font-bold"
                required
            />
        </div>

        <div class="space-y-1">
            <label for="prop-address" class="text-xs font-black uppercase tracking-widest text-gray-400">Address</label>
            <input 
                id="prop-address"
                type="text" 
                bind:value={address}
                placeholder="123 Legal Lane, Suite 101"
                class="w-full px-4 py-3 bg-brand-primary/5 border border-brand-primary/10 rounded-xl focus:ring-2 focus:ring-brand-action outline-none font-bold"
                required
            />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1">
                <label for="prop-price" class="text-xs font-black uppercase tracking-widest text-gray-400">Monthly Rent ($)</label>
                <input 
                    id="prop-price"
                    type="number" 
                    bind:value={price}
                    placeholder="2500"
                    class="w-full px-4 py-3 bg-brand-primary/5 border border-brand-primary/10 rounded-xl focus:ring-2 focus:ring-brand-action outline-none font-bold"
                    required
                />
            </div>
            <div class="space-y-1">
                <label for="prop-type" class="text-xs font-black uppercase tracking-widest text-gray-400">Property Type</label>
                <select 
                    id="prop-type"
                    bind:value={type}
                    class="w-full px-4 py-3 bg-brand-primary/5 border border-brand-primary/10 rounded-xl focus:ring-2 focus:ring-brand-action outline-none font-bold"
                >
                    <option value="Apartment">Apartment</option>
                    <option value="House">House</option>
                    <option value="Studio">Studio</option>
                    <option value="Loft">Loft</option>
                    <option value="Penthouse">Penthouse</option>
                </select>
            </div>
        </div>

        <div class="space-y-1">
            <label for="prop-description" class="text-xs font-black uppercase tracking-widest text-gray-400">Description</label>
            <textarea 
                id="prop-description"
                bind:value={description}
                placeholder="Describe the property's key features..."
                class="w-full px-4 py-3 bg-brand-primary/5 border border-brand-primary/10 rounded-xl focus:ring-2 focus:ring-brand-action outline-none font-medium h-32"
            ></textarea>
        </div>

        <div class="flex gap-3 pt-4">
            <Button variant="secondary" size="xl" class="flex-1" onclick={onClose}>Cancel</Button>
            <Button 
                variant="primary" 
                size="xl" 
                class="flex-[2]"
                type="submit"
                disabled={isSubmitting}
            >
                {#if isSubmitting}
                    Creating...
                {:else}
                    <Plus size={20} />
                    List Property
                {/if}
            </Button>
        </div>
    </form>
</Modal>
