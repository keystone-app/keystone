<script>
    let { 
        children, 
        type = 'default',
        class: className = '' 
    } = $props();

    const types = {
        default: 'bg-gray-100 text-gray-700',
        success: 'bg-green-100 text-green-700',
        info: 'bg-indigo-100 text-indigo-700',
        warning: 'bg-amber-100 text-amber-700',
        error: 'bg-red-100 text-red-700',
        primary: 'bg-indigo-600 text-white'
    };

    let baseClass = $derived(`px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider shadow-sm inline-flex items-center gap-1.5 ${types[type]} ${className}`);
</script>

<span class={baseClass}>
    {@render children()}
</span>
