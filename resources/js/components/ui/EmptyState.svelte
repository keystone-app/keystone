<script>
    import Button from './Button.svelte';

    let { 
        icon, 
        title, 
        message, 
        actionLabel, 
        onAction,
        class: className = '' 
    } = $props();
</script>

<div class="text-center py-20 bg-white rounded-3xl border border-dashed border-gray-200 {className}">
    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
        {#if typeof icon === 'string'}
            <span class="material-symbols-outlined text-gray-300 text-5xl">{icon}</span>
        {:else}
            <!-- Fallback for lucide icons if passed as components -->
            <div class="text-gray-300">
                {@render icon?.()}
            </div>
        {/if}
    </div>
    
    <h3 class="text-2xl font-black text-brand-primary tracking-tight">{title}</h3>
    <p class="text-gray-500 mt-2 max-w-sm mx-auto font-medium">{message}</p>
    
    {#if actionLabel && onAction}
        <Button variant="ghost" class="mt-8 text-brand-action font-black uppercase tracking-widest text-xs" onclick={onAction}>
            {actionLabel}
        </Button>
    {/if}
</div>
