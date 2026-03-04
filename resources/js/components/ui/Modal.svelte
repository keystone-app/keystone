<script>
    import { Dialog } from "bits-ui";
    import { X } from 'lucide-svelte';
    import { fade, scale } from 'svelte/transition';
    import { cn } from "../../lib/utils";

    let { 
        children, 
        title, 
        isOpen, 
        onClose, 
        maxWidth = 'max-w-md',
        zIndex = 'z-[100]',
        showClose = true
    } = $props();
</script>

<Dialog.Root open={isOpen} onOpenChange={(open) => !open && onClose()}>
    <Dialog.Portal>
        <Dialog.Overlay
            transition={fade}
            transitionConfig={{ duration: 200 }}
            class={cn("fixed inset-0 bg-gray-900/60 backdrop-blur-sm", zIndex)}
        />
        <Dialog.Content
            transition={scale}
            transitionConfig={{ duration: 300, start: 0.95 }}
            class={cn(
                "fixed left-[50%] top-[50%] w-full translate-x-[-50%] translate-y-[-50%] rounded-3xl bg-white shadow-2xl outline-none overflow-hidden",
                maxWidth,
                zIndex
            )}
        >
            {#if showClose}
                <Dialog.Close
                    class="absolute right-4 top-4 rounded-full p-2 text-gray-400 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-brand-action z-10"
                >
                    <X size={20} />
                    <span class="sr-only">Close</span>
                </Dialog.Close>
            {/if}

            {#if title}
                <div class="bg-brand-action p-6 text-white">
                    <Dialog.Title class="text-xl font-black uppercase tracking-tight">
                        {title}
                    </Dialog.Title>
                </div>
            {/if}

            <div class="p-8">
                {@render children()}
            </div>
        </Dialog.Content>
    </Dialog.Portal>
</Dialog.Root>
