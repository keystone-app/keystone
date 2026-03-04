<script>
    import { cn } from "../../lib/utils";

    let { role, currentView, onViewChange, currentUser } = $props();

    const navItems = $derived([
        { id: 'listings', label: 'Visual Identity', icon: 'palette' },
        { id: 'dashboard', label: 'Layout & Navigation', icon: 'dashboard', auth: true },
        { id: 'components', label: 'Components', icon: 'category' },
        { id: 'personas', label: 'Personas', icon: 'group' },
    ]);

    // Role-based adaptations based on Section 04 of Design System
    const sidebarTitle = $derived(role === 'landlord' ? 'Landlord Portal' : 'Tenant Portal');
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
            {/if}
        </nav>
    </div>

    <div class="mt-auto p-6 border-t border-white/10">
        {#if currentUser}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-brand-action/20 flex items-center justify-center border border-brand-action/40">
                    <span class="material-symbols-outlined text-brand-action">person</span>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold truncate">{currentUser.name}</p>
                    <p class="text-xs text-gray-400 tracking-tight uppercase">{role}</p>
                </div>
            </div>
        {:else}
            <p class="text-xs text-gray-500 italic">Sign in to access your portal</p>
        {/if}
    </div>
</aside>
