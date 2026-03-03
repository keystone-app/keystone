<script>
    import { Plus, X } from 'lucide-svelte';
    import Modal from '../ui/Modal.svelte';
    import Button from '../ui/Button.svelte';

    let { 
        isOpen, 
        onClose, 
        onRegister, 
        onToggleLogin,
        isSubmitting = false,
        error = ''
    } = $props();

    let name = $state('');
    let email = $state('');
    let password = $state('');

    function handleSubmit(e) {
        e.preventDefault();
        onRegister({ name, email, password });
    }
</script>

<Modal {isOpen} {onClose} zIndex="z-[120]">
    <div class="p-2 space-y-8">
        <div class="text-center space-y-2">
            <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto text-white shadow-lg shadow-indigo-200 mb-6">
                <Plus size={32} />
            </div>
            <h2 class="text-3xl font-black">Create Account</h2>
            <p class="text-gray-500">Join Keystone to schedule your visits</p>
        </div>

        {#if error}
            <div class="bg-red-50 border border-red-100 p-4 rounded-xl flex items-center gap-3">
                <X size={20} class="text-red-600" />
                <p class="text-sm font-bold text-red-700">{error}</p>
            </div>
        {/if}

        <form class="space-y-4" onsubmit={handleSubmit}>
            <div class="space-y-1">
                <label class="text-xs font-black uppercase tracking-widest text-gray-400">Full Name</label>
                <input 
                    type="text" 
                    bind:value={name}
                    placeholder="John Doe"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold"
                    required
                />
            </div>
            <div class="space-y-1">
                <label class="text-xs font-black uppercase tracking-widest text-gray-400">Email Address</label>
                <input 
                    type="email" 
                    bind:value={email}
                    placeholder="your@email.com"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold"
                    required
                />
            </div>
            <div class="space-y-1">
                <label class="text-xs font-black uppercase tracking-widest text-gray-400">Password</label>
                <input 
                    type="password" 
                    bind:value={password}
                    placeholder="••••••••"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold"
                    required
                    minlength="8"
                />
            </div>
            <Button type="submit" variant="primary" size="xl" class="w-full" disabled={isSubmitting}>
                {#if isSubmitting}
                    Creating account...
                {:else}
                    Create Account
                {/if}
            </Button>
        </form>

        <div class="text-center">
            <p class="text-sm text-gray-400 font-bold">Already have an account? <button class="text-indigo-600 hover:underline" onclick={onToggleLogin}>Sign in</button></p>
        </div>
    </div>
</Modal>
