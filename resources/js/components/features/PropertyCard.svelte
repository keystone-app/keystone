<script>
    import { Home } from 'lucide-svelte';
    import Button from '../ui/Button.svelte';
    import Badge from '../ui/Badge.svelte';

    let { property, onViewDetails } = $props();

    const statusTypes = {
        available: 'success',
        rented: 'info',
        maintenance: 'warning'
    };
</script>

<div class="group bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
    <div class="h-56 bg-gray-100 flex items-center justify-center border-b border-gray-100 relative">
        <Home class="w-16 h-16 text-gray-300 group-hover:text-indigo-200 transition-colors" />
        <div class="absolute top-4 left-4">
            <span class="bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold shadow-sm">
                {property.type}
            </span>
        </div>
        <div class="absolute top-4 right-4">
            <Badge type={statusTypes[property.status]}>
                {property.status}
            </Badge>
        </div>
    </div>
    <div class="p-6">
        <h3 class="font-extrabold text-xl mb-1 group-hover:text-indigo-600 transition-colors">{property.name}</h3>
        <p class="text-gray-500 text-sm mb-6 flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
            {property.address}
        </p>
        <div class="flex justify-between items-center pt-5 border-t border-gray-100">
            <div>
                <span class="text-2xl font-black text-indigo-600">${property.price.toLocaleString()}</span>
                <span class="text-gray-400 text-sm font-medium">/mo</span>
            </div>
            <Button variant="outline" size="sm" onclick={() => onViewDetails(property)}>
                View Details
            </Button>
        </div>
    </div>
</div>
