<script>
    import { Search, Filter } from 'lucide-svelte';
    import PropertyCard from './PropertyCard.svelte';
    import PropertyFilters from './PropertyFilters.svelte';
    import Button from '../ui/Button.svelte';
    import EmptyState from '../ui/EmptyState.svelte';

    let { properties, filters, onFilterChange, onPropertySelect } = $props();

    let searchQuery = $state('');

    const filteredProperties = $derived(
        properties.filter(p => {
            const matchesSearch = p.name.toLowerCase().includes(searchQuery.toLowerCase()) || 
                                p.address.toLowerCase().includes(searchQuery.toLowerCase());
            
            return matchesSearch;
        })
    );

    function clearFilters() {
        searchQuery = '';
        onFilterChange({
            min_price: '',
            max_price: '',
            type: '',
            status: ''
        });
    }
</script>

<div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
    <header>
        <h1 class="text-4xl font-extrabold text-brand-primary tracking-tight">Available Listings</h1>
        <p class="text-lg text-gray-500 mt-2 font-medium">Find your next home with verified legal compliance.</p>
    </header>

    <!-- Search and Filters -->
    <div class="space-y-4">
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
            <div class="relative flex-1 w-full">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                <input 
                    type="text" 
                    bind:value={searchQuery}
                    placeholder="Search by property name or address..."
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-action focus:border-brand-action transition-all outline-none font-bold"
                />
            </div>
        </div>

        <PropertyFilters {filters} {onFilterChange} />
    </div>

    {#if filteredProperties.length > 0}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {#each filteredProperties as property}
                <PropertyCard {property} onViewDetails={onPropertySelect} />
            {/each}
        </div>
    {:else}
        <EmptyState 
            icon="search_off"
            title="No properties found"
            message="Try adjusting your filters or search query to find what you're looking for."
            actionLabel="Clear all filters"
            onAction={clearFilters}
        />
    {/if}
</div>
