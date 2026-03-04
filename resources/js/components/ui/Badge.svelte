<script>
    import { cn } from "../../lib/utils";

    let { 
        children, 
        type = 'default',
        class: className = '' 
    } = $props();

    const types = {
        default: 'border-transparent bg-brand-primary/5 text-brand-primary hover:bg-brand-primary/10',
        success: 'border-transparent bg-brand-success/10 text-brand-success hover:bg-brand-success/20',
        info: 'border-transparent bg-brand-action/10 text-brand-action hover:bg-brand-action/20',
        warning: 'border-transparent bg-amber-100 text-amber-700 hover:bg-amber-100/80',
        error: 'border-transparent bg-red-100 text-red-700 hover:bg-red-100/80',
        outline: 'text-brand-primary border-brand-primary/10',
        primary: 'border-transparent bg-brand-primary text-white hover:bg-brand-primary/90'
    };

    const baseClasses = 'inline-flex items-center border px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider transition-colors focus:outline-none focus:ring-2 focus:ring-brand-action focus:ring-offset-2 gap-1.5';
</script>

<div class={cn(baseClasses, types[type], className)}>
    {@render children()}
</div>
