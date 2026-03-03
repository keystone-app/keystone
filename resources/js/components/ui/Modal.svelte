<script>
    import { X } from 'lucide-svelte';
    import { fade, scale } from 'svelte/transition';

    let { 
        children, 
        title, 
        isOpen, 
        onClose, 
        maxWidth = 'max-w-md',
        zIndex = 'z-[100]',
        showClose = true
    } = $props();

    function handleKeydown(e) {
        if (e.key === 'Escape') onClose();
    }
</script>

{#if isOpen}
    <div 
        class="fixed inset-0 {zIndex} flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
        transition:fade={{ duration: 200 }}
        onkeydown={handleKeydown}
        role="dialog"
        aria-modal="true"
    >
        <div 
            class="bg-white w-full {maxWidth} rounded-3xl shadow-2xl overflow-hidden relative"
            transition:scale={{ duration: 300, start: 0.95 }}
        >
            {#if showClose}
                <button 
                    class="absolute top-4 right-4 p-2 hover:bg-gray-100 rounded-full transition-colors z-10"
                    onclick={onClose}
                    aria-label="Close"
                >
                    <X size={20} class="text-gray-400" />
                </button>
            {/if}

            {#if title}
                <div class="bg-indigo-600 p-6 text-white">
                    <h2 class="text-xl font-black uppercase tracking-tight">{title}</h2>
                </div>
            {/if}

            <div class="p-8">
                {@render children()}
            </div>
        </div>
    </div>
{/if}
