<script>
    import { Search, Filter } from 'lucide-svelte';
    import PropertyCard from './PropertyCard.svelte';
    import Button from '../ui/Button.svelte';

    let { properties, onPropertySelect } = $props();

    let searchQuery = $state('');
    let statusFilter = $state('all');

    const filteredProperties = $derived(
        properties.filter(p => {
            const matchesSearch = p.name.toLowerCase().includes(searchQuery.toLowerCase()) || 
                                p.address.toLowerCase().includes(searchQuery.toLowerCase());
            const matchesStatus = statusFilter === 'all' || p.status === statusFilter;
            return matchesSearch && matchesStatus;
        })
    );
</script>

<div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
    <header>
        <h1 class="text-4xl font-extrabold text-brand-primary tracking-tight">Available Listings</h1>
        <p class="text-lg text-gray-500 mt-2">Find your next home with verified legal compliance.</p>
    </header>

    <!-- Search and Filters -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col md:flex-row gap-4 items-center">
        <div class="relative flex-1 w-full">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
            <input 
                type="text" 
                bind:value={searchQuery}
                placeholder="Search by property name or address..."
                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-action focus:border-brand-action transition-all outline-none"
            />
        </div>
        <div class="flex items-center gap-2 w-full md:w-auto">
            <span class="material-symbols-outlined text-gray-400">filter_list</span>
            <select 
                bind:value={statusFilter}
                class="flex-1 md:w-48 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-action focus:border-brand-action outline-none font-semibold text-sm"
            >
                <option value="all">All Statuses</option>
                <option value="available">Available Now</option>
                <option value="rented">Rented</option>
                <option value="maintenance">Maintenance</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        {#each filteredProperties as property}
            <PropertyCard {property} onViewDetails={onPropertySelect} />
        {/each}
    </div>

    {#if filteredProperties.length === 0}
        <div class="text-center py-20 bg-white rounded-xl border border-dashed border-gray-200">
            <span class="material-symbols-outlined text-gray-200 text-6xl mb-4">search_off</span>
            <h3 class="text-xl font-bold text-brand-primary">No properties found</h3>
            <p class="text-gray-500 mt-1">Try adjusting your filters or search query.</p>
            <Button variant="ghost" class="mt-6 text-brand-action font-bold" onclick={() => { searchQuery = ''; statusFilter = 'all'; }}>
                Clear all filters
            </Button>
        </div>
    {/if}
</div>
