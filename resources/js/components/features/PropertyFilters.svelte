<script>
    import { Search, X } from 'lucide-svelte';
    import Button from '../ui/Button.svelte';

    let { filters = { min_price: '', max_price: '', type: '', status: '' }, onFilterChange } = $props();

    function handleInput(field, value) {
        onFilterChange({ ...filters, [field]: value });
    }

    function resetFilters() {
        onFilterChange({
            min_price: '',
            max_price: '',
            type: '',
            status: ''
        });
    }
</script>

<div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Price Range -->
        <div class="space-y-1">
            <label for="min_price" class="text-[10px] font-black uppercase tracking-widest text-gray-400">Min Price</label>
            <input 
                id="min_price"
                type="number" 
                value={filters.min_price} 
                oninput={(e) => handleInput('min_price', e.target.value)}
                placeholder="Min"
                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-brand-action outline-none font-bold text-sm"
            />
        </div>

        <div class="space-y-1">
            <label for="max_price" class="text-[10px] font-black uppercase tracking-widest text-gray-400">Max Price</label>
            <input 
                id="max_price"
                type="number" 
                value={filters.max_price} 
                oninput={(e) => handleInput('max_price', e.target.value)}
                placeholder="Max"
                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-brand-action outline-none font-bold text-sm"
            />
        </div>

        <!-- Property Type -->
        <div class="space-y-1">
            <label for="type" class="text-[10px] font-black uppercase tracking-widest text-gray-400">Property Type</label>
            <select 
                id="type"
                value={filters.type} 
                onchange={(e) => handleInput('type', e.target.value)}
                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-brand-action outline-none font-bold text-sm appearance-none"
            >
                <option value="">All Types</option>
                <option value="apartment">Apartment</option>
                <option value="house">House</option>
                <option value="loft">Loft</option>
                <option value="studio">Studio</option>
            </select>
        </div>

        <!-- Status -->
        <div class="space-y-1">
            <label for="status" class="text-[10px] font-black uppercase tracking-widest text-gray-400">Status</label>
            <select 
                id="status"
                value={filters.status} 
                onchange={(e) => handleInput('status', e.target.value)}
                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-brand-action outline-none font-bold text-sm appearance-none"
            >
                <option value="">All Status</option>
                <option value="available">Available</option>
                <option value="pending">Pending</option>
            </select>
        </div>
    </div>

    <div class="flex justify-end pt-2">
        <Button variant="ghost" size="sm" onclick={resetFilters} class="text-gray-400 hover:text-red-500 font-bold flex items-center gap-2">
            <X size={14} />
            Reset Filters
        </Button>
    </div>
</div>
