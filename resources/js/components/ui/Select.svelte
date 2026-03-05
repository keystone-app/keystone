<script>
    let { 
        id,
        label,
        options = [],
        value = $bindable(),
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
    <select 
        {id}
        bind:value={value}
        class="w-full px-4 py-3 bg-brand-primary/5 border border-brand-primary/10 rounded-xl focus:ring-2 focus:ring-brand-action outline-none font-bold transition-all appearance-none"
        {...rest}
    >
        {#each options as option}
            <option value={option.value || option}>{option.label || option}</option>
        {/each}
    </select>
</div>
