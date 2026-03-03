<script>
    import { Search, Home, FileText, Plus, LogIn, Filter } from 'lucide-svelte';

    let role = $state('tenant'); // 'landlord', 'tenant', 'guest'
    let view = $state('listings'); // 'listings', 'dashboard'
    let isLoggedIn = $state(false);

    let searchQuery = $state('');
    let statusFilter = $state('all');

    const properties = $state([
        { id: 1, name: 'Modern Apartment 101', address: '123 Legal Lane, Suite 101', price: 2500, status: 'available', type: 'Apartment' },
        { id: 2, name: 'Cozy Studio Downtown', address: '456 Urban Ave, #4B', price: 1800, status: 'rented', type: 'Studio' },
        { id: 3, name: 'Spacious Family Home', address: '789 Suburban Way', price: 3500, status: 'available', type: 'House' },
        { id: 4, name: 'Luxury Penthouse', address: '1 Sky High Plaza', price: 5000, status: 'available', type: 'Penthouse' },
        { id: 5, name: 'Rustic Loft', address: '22 Industrial Dr', price: 2200, status: 'maintenance', type: 'Loft' },
    ]);

    const filteredProperties = $derived(
        properties.filter(p => {
            const matchesSearch = p.name.toLowerCase().includes(searchQuery.toLowerCase()) || 
                                p.address.toLowerCase().includes(searchQuery.toLowerCase());
            const matchesStatus = statusFilter === 'all' || p.status === statusFilter;
            return matchesSearch && matchesStatus;
        })
    );

    function toggleAuth() {
        isLoggedIn = !isLoggedIn;
        if (!isLoggedIn) view = 'listings';
    }
</script>

<div class="min-h-screen bg-gray-50 text-gray-900 font-sans antialiased selection:bg-indigo-100">
    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-50">
        <div class="flex items-center gap-8">
            <div class="flex items-center gap-2 cursor-pointer" on:click={() => view = 'listings'}>
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold">K</div>
                <span class="text-xl font-bold tracking-tight">Keystone</span>
            </div>
            
            <div class="hidden md:flex items-center gap-6">
                <button 
                    class="text-sm font-semibold {view === 'listings' ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-900'}"
                    on:click={() => view = 'listings'}
                >
                    Browse Properties
                </button>
                {#if isLoggedIn}
                    <button 
                        class="text-sm font-semibold {view === 'dashboard' ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-900'}"
                        on:click={() => view = 'dashboard'}
                    >
                        My Dashboard
                    </button>
                {/if}
            </div>
        </div>

        <div class="flex items-center gap-6">
            {#if isLoggedIn}
                <div class="flex bg-gray-100 p-1 rounded-md">
                    <button 
                        class="px-4 py-1.5 text-sm font-medium rounded {role === 'landlord' ? 'bg-white shadow-sm text-indigo-600' : 'text-gray-500 hover:text-gray-700'}"
                        on:click={() => role = 'landlord'}
                    >
                        Landlord
                    </button>
                    <button 
                        class="px-4 py-1.5 text-sm font-medium rounded {role === 'tenant' ? 'bg-white shadow-sm text-indigo-600' : 'text-gray-500 hover:text-gray-700'}"
                        on:click={() => role = 'tenant'}
                    >
                        Tenant
                    </button>
                </div>
                <div class="h-8 w-px bg-gray-200"></div>
                <button class="text-gray-500 hover:text-gray-700 font-medium" on:click={toggleAuth}>Log Out</button>
            {:else}
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold flex items-center gap-2 transition-colors" on:click={toggleAuth}>
                    <LogIn size={18} />
                    Sign In
                </button>
            {/if}
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-8">
        {#if view === 'listings'}
            <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
                <header class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div class="space-y-1">
                        <h1 class="text-4xl font-extrabold tracking-tight">Available Listings</h1>
                        <p class="text-gray-500 text-lg">Find your next home with verified legal compliance.</p>
                    </div>
                </header>

                <!-- Search and Filters -->
                <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row gap-4 items-center">
                    <div class="relative flex-1 w-full">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" size={20} />
                        <input 
                            type="text" 
                            bind:value={searchQuery}
                            placeholder="Search by property name or address..."
                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none"
                        />
                    </div>
                    <div class="flex items-center gap-2 w-full md:w-auto">
                        <Filter class="text-gray-400" size={20} />
                        <select 
                            bind:value={statusFilter}
                            class="flex-1 md:w-48 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
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
                        <div class="group bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                            <div class="h-56 bg-gray-100 flex items-center justify-center border-b border-gray-100 relative">
                                <Home class="w-16 h-16 text-gray-300 group-hover:text-indigo-200 transition-colors" />
                                <div class="absolute top-4 left-4">
                                    <span class="bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold shadow-sm">
                                        {property.type}
                                    </span>
                                </div>
                                <div class="absolute top-4 right-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold shadow-sm 
                                        {property.status === 'available' ? 'bg-green-100 text-green-700' : 
                                         property.status === 'rented' ? 'bg-indigo-100 text-indigo-700' : 'bg-amber-100 text-amber-700'}">
                                        {property.status.charAt(0).toUpperCase() + property.status.slice(1)}
                                    </span>
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
                                    <button class="bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white px-4 py-2 rounded-xl text-sm font-bold transition-all">
                                        View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    {/each}
                </div>

                {#if filteredProperties.length === 0}
                    <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-gray-200">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <Search class="text-gray-300" size={40} />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">No properties found</h3>
                        <p class="text-gray-500 mt-1">Try adjusting your filters or search query.</p>
                        <button 
                            class="mt-6 text-indigo-600 font-bold hover:underline"
                            on:click={() => { searchQuery = ''; statusFilter = 'all'; }}
                        >
                            Clear all filters
                        </button>
                    </div>
                {/if}
            </div>
        {:else if isLoggedIn}
            {#if role === 'landlord'}
                <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <header class="flex justify-between items-end">
                        <div>
                            <h1 class="text-3xl font-black">Properties</h1>
                            <p class="text-gray-500 mt-1">Manage your real estate portfolio</p>
                        </div>
                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2 shadow-lg shadow-indigo-200">
                            <Plus size={20} />
                            Add Property
                        </button>
                    </header>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {#each properties.filter(p => p.id <= 3) as property}
                            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                <div class="h-48 bg-gray-50 flex items-center justify-center border-b border-gray-50">
                                    <Home class="w-12 h-12 text-gray-200" />
                                </div>
                                <div class="p-5">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="font-bold text-lg">{property.name}</h3>
                                        <span class="bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded-full">Occupied</span>
                                    </div>
                                    <p class="text-gray-500 text-sm mb-4">{property.address}</p>
                                    <div class="flex justify-between items-center pt-4 border-t border-gray-50">
                                        <span class="font-black text-indigo-600">${property.price.toLocaleString()}/mo</span>
                                        <button class="text-indigo-600 hover:text-indigo-800 text-sm font-bold">Manage</button>
                                    </div>
                                </div>
                            </div>
                        {/each}
                    </div>
                </div>
            {:else}
                <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <header>
                        <h1 class="text-3xl font-black">My Leases</h1>
                        <p class="text-gray-500 mt-1">Review and manage your current rentals</p>
                    </header>

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                                    <FileText size={24} />
                                </div>
                                <div>
                                    <h2 class="font-bold text-lg">Active Lease: Modern Loft A</h2>
                                    <p class="text-gray-500 text-sm font-medium">Expires: Dec 31, 2026</p>
                                </div>
                            </div>
                            <button class="bg-white border border-gray-200 hover:bg-gray-50 px-4 py-2 rounded-xl text-sm font-bold shadow-sm transition-all">Download PDF</button>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div>
                                <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-2">Rent Status</p>
                                <div class="flex items-center gap-2">
                                    <div class="w-2.5 h-2.5 bg-green-500 rounded-full animate-pulse"></div>
                                    <span class="font-bold">Paid for March</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-2">Next Payment</p>
                                <span class="font-bold">$1,850 due on April 1st</span>
                            </div>
                            <div>
                                <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-2">Landlord</p>
                                <span class="font-bold">Apex Realty Group</span>
                            </div>
                        </div>
                    </div>

                    <section class="space-y-4">
                        <h2 class="text-xl font-bold">Documents</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="border-2 border-dashed border-gray-200 rounded-2xl p-8 flex flex-col items-center justify-center text-center hover:border-indigo-300 transition-colors cursor-pointer group bg-white/50">
                                <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mb-4 group-hover:bg-indigo-50 transition-colors">
                                    <Plus class="text-gray-400 group-hover:text-indigo-600" size={24} />
                                </div>
                                <h3 class="font-bold">Upload Document</h3>
                                <p class="text-gray-500 text-sm mt-1">Insurance, ID, or income verification</p>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-2xl p-6 flex items-center justify-between shadow-sm">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600">
                                        <FileText size={20} />
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-sm">Income_Verification.pdf</h4>
                                        <p class="text-gray-400 text-[10px] font-bold mt-0.5">Uploaded 2 days ago</p>
                                    </div>
                                </div>
                                <span class="text-green-600 font-bold text-[10px] bg-green-50 px-2 py-1 rounded-md uppercase tracking-wider">Verified</span>
                            </div>
                        </div>
                    </section>
                </div>
            {/if}
        {/if}
    </main>
</div>

<style>
</style>