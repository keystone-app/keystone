<script>
    import { cn } from "../../lib/utils";

    let { 
        children, 
        type = 'default',
        class: className = '' 
    } = $props();

    const types = {
        default: 'border-transparent bg-gray-100 text-gray-900 hover:bg-gray-100/80',
        success: 'border-transparent bg-green-100 text-green-700 hover:bg-green-100/80',
        info: 'border-transparent bg-indigo-100 text-indigo-700 hover:bg-indigo-100/80',
        warning: 'border-transparent bg-amber-100 text-amber-700 hover:bg-amber-100/80',
        error: 'border-transparent bg-red-100 text-red-700 hover:bg-red-100/80',
        outline: 'text-gray-950 border-gray-200',
        primary: 'border-transparent bg-indigo-600 text-white hover:bg-indigo-600/80'
    };

    const baseClasses = 'inline-flex items-center rounded-full border px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 gap-1.5';
</script>

<div class={cn(baseClasses, types[type], className)}>
    {@render children()}
</div>
