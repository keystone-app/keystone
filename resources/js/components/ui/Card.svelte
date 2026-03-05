<script>
    let { 
        children,
        header,
        footer,
        class: className = '',
        padding = 'p-6',
        hover = true
    } = $props();
</script>

<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm transition-all duration-300 {hover ? 'hover:shadow-xl hover:-translate-y-1' : ''} {className}">
    {#if header}
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            {@render header()}
        </div>
    {/if}

    <div class={padding}>
        {@render children()}
    </div>

    {#if footer}
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
            {@render footer()}
        </div>
    {/if}
</div>
