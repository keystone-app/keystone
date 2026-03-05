<script>
    import { cn } from "../../lib/utils";

    let { 
        role, 
        currentView, 
        landlordView,
        tenantView,
        onViewChange, 
        onLandlordViewChange,
        onTenantViewChange,
        onLogin,
        onRegister,
        onLogout,
        currentUser,
        isLoggedIn = false,
        landlordVisits = [],
        myVisits = [],
        offers = [],
        maintenanceRequests = []
    } = $props();

    const pendingLandlordVisits = $derived(landlordVisits.filter(v => v.status === 'pending').length);
    const pendingOffers = $derived(offers.filter(o => o.status === 'pending').length);
    const pendingMaintenance = $derived(maintenanceRequests.filter(r => role === 'landlord' ? r.status === 'reported' : r.status === 'resolved').length);
</script>

<aside class="w-64 text-white flex-shrink-0 sticky top-0 h-screen hidden lg:flex flex-col border-r border-white/10 bg-brand-primary">
    <div class="p-6">
        <div class="flex items-center gap-2 mb-10">
            <div class="w-8 h-8 bg-brand-action rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-white text-xl">account_balance_wallet</span>
            </div>
            <span class="font-extrabold text-xl tracking-tight">CondoClear</span>
        </div>

        <nav class="space-y-1">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4 px-3">Main Menu</p>
            
            <button 
                onclick={() => onViewChange('listings')}
                class={cn(
                    "w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold transition-all hover:bg-white/5",
                    currentView === 'listings' ? "sidebar-item-active text-white" : "text-gray-400"
                )}
            >
                <span class="material-symbols-outlined">palette</span>
                Browse Properties
            </button>

            {#if currentUser}
                <button 
                    onclick={() => onViewChange('dashboard')}
                    class={cn(
                        "w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold transition-all hover:bg-white/5",
                        currentView === 'dashboard' ? "sidebar-item-active text-white" : "text-gray-400"
                    )}
                >
                    <span class="material-symbols-outlined">dashboard</span>
                    My Dashboard
                </button>

                <!-- Nested Submenu for Landlord Dashboard -->
                {#if currentView === 'dashboard' && role === 'landlord'}
                    <div class="ml-4 mt-2 space-y-1 border-l border-white/10 pl-2">
                        <button 
                            onclick={() => onLandlordViewChange('properties')}
                            class={cn(
                                "w-full flex items-center justify-between px-4 py-2 text-xs font-bold transition-all hover:text-white",
                                landlordView === 'properties' ? "text-brand-action" : "text-gray-500"
                            )}
                        >
                            <span>My Portfolio</span>
                        </button>
                        <button 
                            onclick={() => onLandlordViewChange('visits')}
                            class={cn(
                                "w-full flex items-center justify-between px-4 py-2 text-xs font-bold transition-all hover:text-white",
                                landlordView === 'visits' ? "text-brand-action" : "text-gray-500"
                            )}
                        >
                            <span>Visit Requests</span>
                            {#if pendingLandlordVisits > 0}
                                <span class="bg-brand-action text-white text-[8px] px-1.5 py-0.5 rounded-full">{pendingLandlordVisits}</span>
                            {/if}
                        </button>
                        <button 
                            onclick={() => onLandlordViewChange('offers')}
                            class={cn(
                                "w-full flex items-center justify-between px-4 py-2 text-xs font-bold transition-all hover:text-white",
                                landlordView === 'offers' ? "text-brand-action" : "text-gray-500"
                            )}
                        >
                            <span>Offers</span>
                            {#if pendingOffers > 0}
                                <span class="bg-brand-action text-white text-[8px] px-1.5 py-0.5 rounded-full">{pendingOffers}</span>
                            {/if}
                        </button>
                        <button 
                            onclick={() => onLandlordViewChange('maintenance')}
                            class={cn(
                                "w-full flex items-center justify-between px-4 py-2 text-xs font-bold transition-all hover:text-white",
                                landlordView === 'maintenance' ? "text-brand-action" : "text-gray-500"
                            )}
                        >
                            <span>Maintenance</span>
                            {#if pendingMaintenance > 0}
                                <span class="bg-orange-500 text-white text-[8px] px-1.5 py-0.5 rounded-full">{pendingMaintenance}</span>
                            {/if}
                        </button>
                    </div>
                {/if}

                <!-- Nested Submenu for Tenant Dashboard -->
                {#if currentView === 'dashboard' && role === 'tenant'}
                    <div class="ml-4 mt-2 space-y-1 border-l border-white/10 pl-2">
                        <button 
                            onclick={() => onTenantViewChange('visits')}
                            class={cn(
                                "w-full flex items-center justify-between px-4 py-2 text-xs font-bold transition-all hover:text-white",
                                tenantView === 'visits' ? "text-brand-action" : "text-gray-500"
                            )}
                        >
                            <span>Scheduled Visits</span>
                        </button>
                        <button 
                            onclick={() => onTenantViewChange('offers')}
                            class={cn(
                                "w-full flex items-center justify-between px-4 py-2 text-xs font-bold transition-all hover:text-white",
                                tenantView === 'offers' ? "text-brand-action" : "text-gray-500"
                            )}
                        >
                            <span>Negotiations</span>
                        </button>
                        <button 
                            onclick={() => onTenantViewChange('leases')}
                            class={cn(
                                "w-full flex items-center justify-between px-4 py-2 text-xs font-bold transition-all hover:text-white",
                                tenantView === 'leases' ? "text-brand-action" : "text-gray-500"
                            )}
                        >
                            <span>My Leases</span>
                        </button>
                        <button 
                            onclick={() => onTenantViewChange('maintenance')}
                            class={cn(
                                "w-full flex items-center justify-between px-4 py-2 text-xs font-bold transition-all hover:text-white",
                                tenantView === 'maintenance' ? "text-brand-action" : "text-gray-500"
                            )}
                        >
                            <span>Maintenance</span>
                            {#if role === 'tenant' && pendingMaintenance > 0}
                                <span class="bg-brand-success text-white text-[8px] px-1.5 py-0.5 rounded-full">{pendingMaintenance}</span>
                            {/if}
                        </button>
                    </div>
                {/if}
            {/if}
        </nav>
    </div>

    <div class="mt-auto p-6 border-t border-white/10">
        {#if isLoggedIn && currentUser}
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-full bg-brand-action/20 flex items-center justify-center border border-brand-action/40">
                        <span class="material-symbols-outlined text-brand-action">person</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold truncate">{currentUser.name}</p>
                        <p class="text-xs text-gray-400 tracking-tight uppercase">{role}</p>
                    </div>
                </div>
                <button 
                    onclick={onLogout}
                    class="p-2 text-gray-400 hover:text-white transition-colors"
                    aria-label="Logout"
                >
                    <span class="material-symbols-outlined text-sm">logout</span>
                </button>
            </div>
        {:else}
            <div class="space-y-3">
                <p class="text-xs text-gray-500 italic mb-4">Sign in to access your portal</p>
                <button 
                    onclick={onLogin}
                    class="w-full py-2 px-4 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl text-xs font-bold transition-all"
                >
                    Sign In
                </button>
                <button 
                    onclick={onRegister}
                    class="w-full py-2 px-4 bg-brand-action text-white rounded-xl text-xs font-bold transition-all shadow-lg shadow-brand-action/20"
                >
                    Create Account
                </button>
            </div>
        {/if}
    </div>
</aside>
