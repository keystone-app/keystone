<script>
    import { cn } from "../../lib/utils";

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
        primary: 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm',
        secondary: 'bg-white border border-gray-200 text-gray-900 hover:bg-gray-50',
        outline: 'border border-indigo-600 text-indigo-600 hover:bg-indigo-50',
        danger: 'bg-red-600 text-white hover:bg-red-700',
        ghost: 'text-gray-600 hover:bg-gray-100 hover:text-gray-900',
        link: 'text-indigo-600 underline-offset-4 hover:underline',
        tab: 'px-6 py-3 text-sm font-bold border-b-2 transition-all'
    };

    const sizes = {
        sm: 'h-8 px-3 text-xs',
        md: 'h-10 px-4 py-2 rounded-lg',
        lg: 'h-12 px-8 rounded-xl font-bold',
        xl: 'h-14 px-10 rounded-2xl font-black text-lg',
        icon: 'h-10 w-10'
    };

    const baseClasses = 'inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-indigo-500 disabled:pointer-events-none disabled:opacity-50 gap-2';
</script>

<button 
    {type} 
    {disabled} 
    class={cn(baseClasses, variants[variant], sizes[size], className)} 
    {onclick} 
    {...rest}
>
    {@render children()}
</button>
