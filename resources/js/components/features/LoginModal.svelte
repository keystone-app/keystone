<script>
    import { LogIn, X } from 'lucide-svelte';
    import Modal from '../ui/Modal.svelte';
    import Button from '../ui/Button.svelte';

    let { 
        isOpen, 
        onClose, 
        onLogin, 
        onToggleRegister,
        isSubmitting = false,
        error = ''
    } = $props();

    let email = $state('alice@landlord.com');
    let password = $state('password');

    function handleSubmit(e) {
        e.preventDefault();
        onLogin({ email, password });
    }
</script>

<Modal {isOpen} {onClose} zIndex="z-[120]">
    <div class="space-y-8">
        <div class="text-center space-y-2">
            <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto text-white shadow-lg shadow-indigo-200 mb-6">
                <LogIn size={32} />
            </div>
            <h2 class="text-3xl font-black">Welcome back</h2>
            <p class="text-gray-500">Sign in to access your Keystone dashboard</p>
        </div>

        {#if error}
            <div class="bg-red-50 border border-red-100 p-4 rounded-xl flex items-center gap-3">
                <X size={20} class="text-red-600" />
                <p class="text-sm font-bold text-red-700">{error}</p>
            </div>
        {/if}

        <form class="space-y-4" onsubmit={handleSubmit}>
            <div class="space-y-1">
                <label for="login-email" class="text-xs font-black uppercase tracking-widest text-gray-400">Email Address</label>
                <input 
                    id="login-email"
                    type="email" 
                    bind:value={email}
                    placeholder="your@email.com"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold"
                    required
                />
            </div>
            <div class="space-y-1">
                <label for="login-password" class="text-xs font-black uppercase tracking-widest text-gray-400">Password</label>
                <input 
                    id="login-password"
                    type="password" 
                    bind:value={password}
                    placeholder="••••••••"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold"
                    required
                />
            </div>
            <Button type="submit" variant="primary" size="xl" class="w-full" disabled={isSubmitting}>
                {#if isSubmitting}
                    Signing in...
                {:else}
                    Sign In
                {/if}
            </Button>
        </form>

        <div class="text-center">
            <p class="text-sm text-gray-400 font-bold">Don't have an account? <button class="text-indigo-600 hover:underline" onclick={onToggleRegister}>Create one</button></p>
        </div>
    </div>
</Modal>
