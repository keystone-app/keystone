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
        primary: 'bg-brand-action text-white hover:opacity-90 shadow-sm',
        secondary: 'bg-white border border-brand-primary/10 text-brand-primary hover:bg-brand-bg',
        outline: 'border border-brand-action text-brand-action hover:bg-brand-action/5',
        danger: 'bg-red-600 text-white hover:bg-red-700',
        ghost: 'text-brand-primary/60 hover:bg-brand-primary/5 hover:text-brand-primary',
        link: 'text-brand-action underline-offset-4 hover:underline',
        tab: 'px-6 py-3 text-sm font-bold border-b-2 transition-all'
    };

    const sizes = {
        sm: 'h-8 px-3 text-xs',
        md: 'h-10 px-4 py-2 font-semibold',
        lg: 'h-12 px-8 font-bold',
        xl: 'h-14 px-10 font-black text-lg',
        icon: 'h-10 w-10'
    };

    const baseClasses = 'inline-flex items-center justify-center whitespace-nowrap text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-brand-action disabled:pointer-events-none disabled:opacity-50 gap-2';
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
