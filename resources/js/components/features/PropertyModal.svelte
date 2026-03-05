<script>
    import Modal from '../ui/Modal.svelte';
    import Button from '../ui/Button.svelte';
    import Input from '../ui/Input.svelte';
    import TextArea from '../ui/TextArea.svelte';
    import Select from '../ui/Select.svelte';
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
    let images = $state([]);
    let videos = $state([]);
    let imagePreviews = $state([]);

    const propertyTypes = [
        'Apartment', 'House', 'Studio', 'Loft', 'Penthouse'
    ];

    function handleImageChange(e) {
        const files = Array.from(e.target.files);
        images = [...images, ...files];
        
        files.forEach(file => {
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreviews.push(e.target.result);
            };
            reader.readAsDataURL(file);
        });
    }

    function handleVideoChange(e) {
        const files = Array.from(e.target.files);
        videos = [...videos, ...files];
    }

    function removeImage(index) {
        images = images.filter((_, i) => i !== index);
        imagePreviews = imagePreviews.filter((_, i) => i !== index);
    }

    function removeVideo(index) {
        videos = videos.filter((_, i) => i !== index);
    }

    async function handleSubmit(e) {
        e.preventDefault();
        const success = await onSubmit({
            name,
            address,
            price: parseFloat(price),
            type,
            description,
            images,
            videos
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
        images = [];
        videos = [];
        imagePreviews = [];
    }
</script>

<Modal {isOpen} {onClose} title="Add New Property" maxWidth="max-w-lg">
    <form class="space-y-6" onsubmit={handleSubmit}>
        <Input 
            id="prop-name"
            label="Property Name"
            bind:value={name}
            placeholder="Modern Apartment 101"
            required
        />

        <Input 
            id="prop-address"
            label="Address"
            bind:value={address}
            placeholder="123 Legal Lane, Suite 101"
            required
        />

        <div class="grid grid-cols-2 gap-4">
            <Input 
                id="prop-price"
                label="Monthly Rent ($)"
                type="number"
                bind:value={price}
                placeholder="2500"
                required
            />
            <Select 
                id="prop-type"
                label="Property Type"
                options={propertyTypes}
                bind:value={type}
            />
        </div>

        <TextArea 
            id="prop-description"
            label="Description"
            bind:value={description}
            placeholder="Describe the property's key features..."
            class="h-32"
        />

        <div class="space-y-3">
            <label class="text-xs font-black uppercase tracking-widest text-gray-400">Media Assets</label>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <input id="prop-images" type="file" multiple accept="image/*" class="hidden" onchange={handleImageChange} />
                    <label for="prop-images" class="block p-4 border-2 border-dashed border-brand-primary/10 rounded-xl hover:border-brand-action cursor-pointer transition-colors text-center">
                        <span class="text-xs font-black uppercase text-brand-action">Add Images</span>
                    </label>
                </div>
                <div class="space-y-2">
                    <input id="prop-videos" type="file" multiple accept="video/*" class="hidden" onchange={handleVideoChange} />
                    <label for="prop-videos" class="block p-4 border-2 border-dashed border-brand-primary/10 rounded-xl hover:border-brand-action cursor-pointer transition-colors text-center">
                        <span class="text-xs font-black uppercase text-brand-action">Add Videos</span>
                    </label>
                </div>
            </div>

            {#if imagePreviews.length > 0}
                <div class="grid grid-cols-4 gap-2">
                    {#each imagePreviews as preview, i}
                        <div class="relative aspect-square bg-gray-100 rounded-lg overflow-hidden group">
                            <img src={preview} alt="Preview" class="w-full h-full object-cover" />
                            <button 
                                type="button"
                                onclick={() => removeImage(i)}
                                class="absolute top-1 right-1 bg-black/50 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity"
                            >
                                <X size={12} />
                            </button>
                        </div>
                    {/each}
                </div>
            {/if}

            {#if videos.length > 0}
                <div class="space-y-1">
                    {#each videos as video, i}
                        <div class="flex items-center justify-between p-2 bg-brand-primary/5 rounded-lg text-xs font-bold">
                            <span class="truncate max-w-[200px]">{video.name}</span>
                            <button type="button" onclick={() => removeVideo(i)} class="text-red-500">
                                <X size={14} />
                            </button>
                        </div>
                    {/each}
                </div>
            {/if}
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
