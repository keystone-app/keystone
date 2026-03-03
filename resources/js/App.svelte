<script>
    let role = 'landlord'; // Initial role for development, will be handled by auth later
</script>

<div class="min-h-screen bg-gray-50 text-gray-900 font-sans">
    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-50">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold">K</div>
            <span class="text-xl font-bold tracking-tight">Keystone</span>
        </div>
        <div class="flex items-center gap-6">
            <div class="flex bg-gray-100 p-1 rounded-md">
                <button 
                    class="px-4 py-1.5 text-sm font-medium rounded {role === 'landlord' ? 'bg-white shadow-sm' : 'text-gray-500 hover:text-gray-700'}"
                    on:click={() => role = 'landlord'}
                >
                    Landlord
                </button>
                <button 
                    class="px-4 py-1.5 text-sm font-medium rounded {role === 'tenant' ? 'bg-white shadow-sm' : 'text-gray-500 hover:text-gray-700'}"
                    on:click={() => role = 'tenant'}
                >
                    Tenant
                </button>
            </div>
            <div class="h-8 w-px bg-gray-200"></div>
            <button class="text-gray-500 hover:text-gray-700 font-medium">Log Out</button>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-8">
        {#if role === 'landlord'}
            <div class="space-y-8 animate-in fade-in duration-500">
                <header class="flex justify-between items-end">
                    <div>
                        <h1 class="text-3xl font-bold">Properties</h1>
                        <p class="text-gray-500 mt-1">Manage your real estate portfolio</p>
                    </div>
                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-semibold transition-colors flex items-center gap-2 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-7-7v14"/></svg>
                        Add Property
                    </button>
                </header>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {#each [1, 2, 3] as property}
                        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                            <div class="h-48 bg-gray-100 flex items-center justify-center border-b border-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            </div>
                            <div class="p-5">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-bold text-lg">Modern Apartment {property}</h3>
                                    <span class="bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded-full">Occupied</span>
                                </div>
                                <p class="text-gray-500 text-sm mb-4">123 Legal Lane, Suite {property}00</p>
                                <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                                    <span class="font-bold text-indigo-600">$2,500/mo</span>
                                    <button class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">View Details</button>
                                </div>
                            </div>
                        </div>
                    {/each}
                </div>
            </div>
        {:else}
            <div class="space-y-8 animate-in fade-in duration-500">
                <header>
                    <h1 class="text-3xl font-bold">My Leases</h1>
                    <p class="text-gray-500 mt-1">Review and manage your current rentals</p>
                </header>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            </div>
                            <div>
                                <h2 class="font-bold text-lg">Active Lease: Modern Loft A</h2>
                                <p class="text-gray-500 text-sm">Expires: Dec 31, 2026</p>
                            </div>
                        </div>
                        <button class="bg-white border border-gray-200 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-semibold shadow-sm">Download PDF</button>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div>
                            <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-2">Rent Status</p>
                            <div class="flex items-center gap-2">
                                <div class="w-2.5 h-2.5 bg-green-500 rounded-full"></div>
                                <span class="font-bold">Paid for March</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-2">Next Payment</p>
                            <span class="font-bold">$1,850 due on April 1st</span>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-2">Landlord</p>
                            <span class="font-bold">Apex Realty Group</span>
                        </div>
                    </div>
                </div>

                <section class="space-y-4">
                    <h2 class="text-xl font-bold">Documents</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 flex flex-col items-center justify-center text-center hover:border-indigo-300 transition-colors cursor-pointer group">
                            <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mb-4 group-hover:bg-indigo-50 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400 group-hover:text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            </div>
                            <h3 class="font-bold">Upload Document</h3>
                            <p class="text-gray-500 text-sm mt-1">Insurance, ID, or income verification</p>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-xl p-6 flex items-center justify-between shadow-sm">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-sm">Income_Verification.pdf</h4>
                                    <p class="text-gray-400 text-xs">Uploaded 2 days ago</p>
                                </div>
                            </div>
                            <span class="text-green-600 font-bold text-xs bg-green-50 px-2 py-1 rounded">Verified</span>
                        </div>
                    </div>
                </section>
            </div>
        {/if}
    </main>
</div>

<style>
    :global(body) {
        @apply antialiased;
    }
</style>