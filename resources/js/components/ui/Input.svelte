<script>
    let { 
        id,
        label,
        type = 'text',
        placeholder = '',
        value = $bindable(),
        required = false,
        class: className = '',
        ...rest
    } = $props();
</script>

<div class="space-y-1 {className}">
    {#if label}
        <label for={id} class="text-xs font-black uppercase tracking-widest text-gray-400">
            {label}
        </label>
    {/if}
    <input 
        {id}
        {type}
        {placeholder}
        bind:value={value}
        {required}
        class="w-full px-4 py-3 bg-brand-primary/5 border border-brand-primary/10 rounded-xl focus:ring-2 focus:ring-brand-action outline-none font-bold placeholder:text-gray-300 transition-all"
        {...rest}
    />
</div>
