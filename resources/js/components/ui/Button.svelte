<script>
    let { 
        children, 
        variant = 'primary', 
        size = 'md', 
        type = 'button',
        disabled = false,
        class: className = '',
        onclick,
        ...rest 
    } = $props();

    const variants = {
        primary: 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-lg shadow-indigo-200',
        secondary: 'bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 shadow-sm',
        outline: 'bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white',
        danger: 'bg-red-600 hover:bg-red-700 text-white',
        ghost: 'text-gray-500 hover:text-gray-900',
        tab: 'px-6 py-3 text-sm font-bold border-b-2 transition-all'
    };

    const sizes = {
        sm: 'px-3 py-1.5 text-xs',
        md: 'px-4 py-2 rounded-lg font-semibold',
        lg: 'px-5 py-2.5 rounded-xl font-bold',
        xl: 'py-4 rounded-2xl font-black text-lg'
    };

    let baseClass = $derived(`transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed ${variants[variant]} ${sizes[size]} ${className}`);
</script>

<button {type} {disabled} class={baseClass} {onclick} {...rest}>
    {@render children()}
</button>
