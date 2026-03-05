<script>
    let { 
        price, 
        currency = '$', 
        suffix = '/mo',
        size = 'md',
        class: className = '' 
    } = $props();

    const formattedPrice = $derived(parseFloat(price).toLocaleString());

    const sizes = {
        sm: 'text-lg',
        md: 'text-2xl',
        lg: 'text-3xl',
        xl: 'text-5xl'
    };
</script>

<div class="inline-flex items-baseline gap-0.5 {className}">
    <span class="font-black text-brand-action {sizes[size]}">
        {currency}{formattedPrice}
    </span>
    <span class="text-gray-400 font-bold {size === 'xl' ? 'text-lg' : 'text-xs'}">
        {suffix}
    </span>
</div>
