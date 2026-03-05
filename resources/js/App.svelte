<script>
    import { onMount } from 'svelte';
    import { LogIn } from 'lucide-svelte';
    import Button from './components/ui/Button.svelte';
    import Sidebar from './components/features/Sidebar.svelte';
    import ListingsView from './components/features/ListingsView.svelte';
    import PropertyDetailView from './components/features/PropertyDetailView.svelte';
    import LandlordDashboard from './components/features/LandlordDashboard.svelte';
    import TenantDashboard from './components/features/TenantDashboard.svelte';
    import OfferModal from './components/features/OfferModal.svelte';
    import VisitModal from './components/features/VisitModal.svelte';
    import PropertyModal from './components/features/PropertyModal.svelte';
    import LoginModal from './components/features/LoginModal.svelte';
    import RegisterModal from './components/features/RegisterModal.svelte';
    import MaintenanceRequestModal from './components/features/MaintenanceRequestModal.svelte';

    // Global State
    let role = $state('guest');
    let view = $state('listings'); 
    let landlordView = $state('properties');
    let tenantView = $state('visits');
    let isLoggedIn = $state(false);
    let currentUser = $state(null);
    let identityDoc = $state(null);

    // Data State
    let properties = $state([]);
    let filters = $state({ min_price: '', max_price: '', type: '', status: '' });
    let landlordVisits = $state([]);
    let myVisits = $state([]);
    let offers = $state([]);
    let maintenanceRequests = $state([]);
    let myLeases = $state([]);

    // UI State
    let selectedProperty = $state(null);
    let isScheduling = $state(false);
    let showPropertyModal = $state(false);
    let showLoginModal = $state(false);
    let showRegisterModal = $state(false);
    let showOfferModal = $state(false);
    let showMaintenanceModal = $state(false);
    let selectedLeaseForMaintenance = $state(null);
    let selectedVisitForOffer = $state(null);
    let loginError = $state('');
    let isSubmitting = $state(false);

    // Lifecycle
    onMount(async () => {
        await checkAuth();
        await fetchProperties();
    });

    // API Actions
    async function checkAuth() {
        try {
            const res = await fetch('/me');
            const data = await res.json();
            if (data.user) {
                isLoggedIn = true;
                currentUser = data.user;
                role = data.role;
                identityDoc = data.identity_document;
                fetchOffers();
                fetchMyVisits();
                fetchMaintenanceRequests();
                fetchMyLeases();
                if (role === 'landlord') fetchLandlordVisits();
            }
        } catch (e) {
            console.error('Auth check failed', e);
        }
    }

    async function fetchProperties() {
        try {
            const params = new URLSearchParams();
            if (filters.min_price) params.append('min_price', filters.min_price);
            if (filters.max_price) params.append('max_price', filters.max_price);
            if (filters.type) params.append('type', filters.type);
            if (filters.status) params.append('status', filters.status);

            const res = await fetch(`/properties?${params.toString()}`);
            if (res.ok) {
                properties = await res.json();
            }
        } catch (e) {
            console.error('Failed to fetch properties', e);
        }
    }

    async function fetchMyVisits() {
        try {
            const res = await fetch('/my-visits');
            if (res.ok) myVisits = await res.json();
        } catch (e) {}
    }

    async function fetchOffers() {
        try {
            const res = await fetch('/offers');
            if (res.ok) offers = await res.json();
        } catch (e) {}
    }

    async function fetchLandlordVisits() {
        try {
            const res = await fetch('/visits');
            if (res.ok) landlordVisits = await res.json();
        } catch (e) {}
    }

    async function fetchMaintenanceRequests() {
        try {
            const res = await fetch('/maintenance');
            if (res.ok) maintenanceRequests = await res.json();
        } catch (e) {}
    }

    async function fetchMyLeases() {
        try {
            const res = await fetch('/leases');
            if (res.ok) myLeases = await res.json();
        } catch (e) {}
    }

    async function logout() {
        try {
            await fetch('/logout', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
            });
            window.location.reload();
        } catch (e) {
            window.location.reload();
        }
    }

    async function login({ email, password }) {
        loginError = '';
        isSubmitting = true;
        try {
            const res = await fetch('/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            if (res.ok) {
                const data = await res.json();
                isLoggedIn = true;
                currentUser = data.user;
                role = data.role;
                updateCsrfToken(data.csrf_token);
                showLoginModal = false;
                fetchOffers();
                fetchMyVisits();
                fetchMaintenanceRequests();
                fetchMyLeases();
                if (role === 'landlord') fetchLandlordVisits();
            } else {
                const data = await res.json();
                loginError = data.message || 'Login failed.';
            }
        } catch (e) {
            loginError = 'Connection error.';
        } finally {
            isSubmitting = false;
        }
    }

    async function register({ name, email, password }) {
        loginError = '';
        isSubmitting = true;
        try {
            const res = await fetch('/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: JSON.stringify({ name, email, password })
            });
            if (res.ok) {
                const data = await res.json();
                isLoggedIn = true;
                currentUser = data.user;
                role = data.role;
                updateCsrfToken(data.csrf_token);
                showRegisterModal = false;
                fetchOffers();
                fetchMyVisits();
                fetchMaintenanceRequests();
                fetchMyLeases();
            } else {
                const data = await res.json();
                loginError = data.message || 'Registration failed.';
            }
        } catch (e) {
            loginError = 'Connection error.';
        } finally {
            isSubmitting = false;
        }
    }

    async function storeProperty(data) {
        isSubmitting = true;
        try {
            const formData = new FormData();
            formData.append('name', data.name);
            formData.append('address', data.address);
            formData.append('price', data.price);
            formData.append('type', data.type);
            formData.append('description', data.description || '');
            
            if (data.images && data.images.length > 0) {
                data.images.forEach(image => {
                    formData.append('images[]', image);
                });
            }
            
            if (data.videos && data.videos.length > 0) {
                data.videos.forEach(video => {
                    formData.append('videos[]', video);
                });
            }

            const res = await fetch('/properties', {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 
                    'Accept': 'application/json' 
                },
                body: formData
            });
            if (res.ok) {
                const newProperty = await res.json();
                properties = [...properties, { ...newProperty, compliance: { gas: 'Pending', fire: 'Pending', electric: 'Pending' }, features: [] }];
                return true;
            }
            return false;
        } catch (e) {
            return false;
        } finally {
            isSubmitting = false;
        }
    }

    async function submitVisit({ date, time, file }) {
        isSubmitting = true;
        try {
            let docId = identityDoc?.id;
            if (file) {
                const formData = new FormData();
                formData.append('file', file);
                const uploadRes = await fetch('/identity-upload', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: formData
                });
                if (uploadRes.ok) {
                    const uploadedDoc = await uploadRes.json();
                    docId = uploadedDoc.id;
                    identityDoc = uploadedDoc;
                }
            }
            const visitRes = await fetch('/visits', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: JSON.stringify({ property_id: selectedProperty.id, document_id: docId, visit_at: `${date} ${time}` })
            });
            if (visitRes.ok) {
                const newVisit = await visitRes.json();
                myVisits = [...myVisits, { ...newVisit, property: selectedProperty }];
                return true;
            }
            return false;
        } catch (e) {
            return false;
        } finally {
            isSubmitting = false;
        }
    }

    async function submitOffer(visitId, amount, terms) {
        try {
            const res = await fetch('/offers', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: JSON.stringify({ visit_id: visitId, amount, terms })
            });
            if (res.ok) {
                const newOffer = await res.json();
                offers = [newOffer, ...offers];
                alert('Offer submitted!');
                return true;
            }
            return false;
        } catch (e) {
            return false;
        }
    }

    async function submitMaintenanceRequest({ lease_id, title, description }) {
        isSubmitting = true;
        try {
            const res = await fetch('/maintenance', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: JSON.stringify({ lease_id, title, description })
            });
            if (res.ok) {
                const newRequest = await res.json();
                maintenanceRequests = [newRequest, ...maintenanceRequests];
                return true;
            }
            return false;
        } catch (e) {
            return false;
        } finally {
            isSubmitting = false;
        }
    }

    async function updateMaintenanceStatus(requestId, status) {
        try {
            const res = await fetch(`/maintenance/${requestId}`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: JSON.stringify({ status })
            });
            if (res.ok) {
                const updated = await res.json();
                maintenanceRequests = maintenanceRequests.map(r => r.id === requestId ? updated : r);
            }
        } catch (e) {}
    }

    async function updateOfferStatus(offerId, status, amount = null, terms = null) {
        try {
            const res = await fetch(`/offers/${offerId}`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: JSON.stringify({ status, amount, terms })
            });
            if (res.ok) {
                const updatedOffer = await res.json();
                offers = offers.map(o => o.id === offerId ? updatedOffer : o);
            }
        } catch (e) {}
    }

    async function handleComplianceUpload(offerId, type, e) {
        const file = e.target.files[0];
        if (!file) return;
        isSubmitting = true;
        const formData = new FormData();
        formData.append('file', file);
        formData.append('type', type);
        formData.append('offer_id', offerId);
        try {
            const res = await fetch('/compliance-upload', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: formData
            });
            if (res.ok) await fetchOffers();
        } catch (e) {} finally { isSubmitting = false; }
    }

    async function handleIncomeVerification(offerId) {
        isSubmitting = true;
        try {
            const res = await fetch(`/offers/${offerId}/verify`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
            });
            if (res.ok) await fetchOffers();
        } catch (e) {} finally { isSubmitting = false; }
    }

    function updateCsrfToken(newToken) {
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) meta.content = newToken;
    }

    function startScheduling() {
        if (!isLoggedIn) {
            showLoginModal = true;
            return;
        }
        isScheduling = true;
    }

    function updateVisitStatus(visitId, newStatus) {
        // Simple wrapper for table actions
        fetch(`/visits/${visitId}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: JSON.stringify({ status: newStatus })
        }).then(async res => {
            if (res.ok) {
                const updated = await res.json();
                landlordVisits = landlordVisits.map(v => v.id === visitId ? updated : v);
            }
        });
    }
</script>

<div class="flex min-h-screen bg-brand-bg text-brand-primary font-sans antialiased selection:bg-brand-action selection:text-white">
    {#if isLoggedIn && currentUser}
        <Sidebar 
            {role} 
            currentView={view} 
            {landlordView}
            {tenantView}
            {currentUser}
            {landlordVisits}
            {offers}
            {maintenanceRequests}
            onViewChange={(newView) => { view = newView; selectedProperty = null; }} 
            onLandlordViewChange={(v) => landlordView = v}
            onTenantViewChange={(v) => tenantView = v}
        />
    {/if}

    <main class="flex-1 flex flex-col min-w-0">
        <header class="h-16 bg-white border-b border-gray-200 sticky top-0 z-30 flex items-center justify-between px-8 lg:px-12">
            <div class="flex items-center gap-2 text-sm font-medium">
                <span class="text-gray-400 capitalize">{view}</span>
                {#if selectedProperty}
                    <span class="material-symbols-outlined text-gray-300 text-sm">chevron_right</span>
                    <span class="truncate max-w-[200px]">{selectedProperty.name}</span>
                {/if}
            </div>

            <div class="flex items-center gap-6">
                {#if isLoggedIn && currentUser}
                    <div class="flex items-center bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 gap-3">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Role</span>
                        <div class="flex gap-2">
                            <button class="text-xs font-bold transition-colors {role === 'landlord' ? 'text-brand-action' : 'text-gray-400 hover:text-gray-600'}" onclick={() => role = 'landlord'}>Landlord</button>
                            <div class="w-px h-3 bg-gray-300"></div>
                            <button class="text-xs font-bold transition-colors {role !== 'landlord' ? 'text-brand-action' : 'text-gray-400 hover:text-gray-600'}" onclick={() => role = 'tenant'}>Tenant</button>
                        </div>
                    </div>
                    <div class="h-8 w-px bg-gray-200"></div>
                    <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 font-bold transition-all shadow-lg shadow-red-100 text-xs" onclick={logout}>Log Out</button>
                {:else}
                    <Button variant="primary" size="sm" onclick={() => showLoginModal = true}>
                        <LogIn size={16} />
                        Sign In
                    </Button>
                {/if}
            </div>
        </header>

        <div class="p-8 lg:p-12 max-w-6xl mx-auto w-full">
            {#if view === 'listings'}
                {#if selectedProperty}
                    <PropertyDetailView 
                        property={selectedProperty} 
                        onBack={() => selectedProperty = null} 
                        onScheduleVisit={startScheduling} 
                    />
                {:else}
                    <ListingsView {properties} onPropertySelect={(p) => { selectedProperty = p; window.scrollTo({ top: 0, behavior: 'smooth' }); }} />
                {/if}
            {:else if isLoggedIn}
                {#if role === 'landlord'}
                    <LandlordDashboard 
                        {landlordView} 
                        properties={properties.filter(p => p.user_id === currentUser?.id)} 
                        {landlordVisits} 
                        {offers} 
                        {maintenanceRequests}
                        onAddProperty={() => showPropertyModal = true}
                        onApproveVisit={(id) => updateVisitStatus(id, 'scheduled')}
                        onRejectVisit={(id) => updateVisitStatus(id, 'rejected')}
                        onUpdateOfferStatus={updateOfferStatus}
                        onUpdateMaintenanceStatus={updateMaintenanceStatus}
                    />
                {:else}
                    <TenantDashboard 
                        {tenantView} 
                        {myVisits} 
                        {offers} 
                        {identityDoc}
                        {maintenanceRequests}
                        {myLeases}
                        onMakeOffer={(v) => { selectedVisitForOffer = v; showOfferModal = true; }}
                        onReportMaintenance={(l) => { selectedLeaseForMaintenance = l; showMaintenanceModal = true; }}
                        onUploadCompliance={handleComplianceUpload}
                        onVerifyIncome={handleIncomeVerification}
                    />
                {/if}
            {/if}
        </div>

        <footer class="mt-auto border-t border-gray-200 bg-white py-8 px-12">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-gray-400">© 2026 CondoClear. Built for Compliance and Clarity.</p>
                <div class="flex gap-6">
                    <button class="text-sm font-bold text-brand-action hover:underline">Reference</button>
                    <button class="text-sm font-bold text-brand-action hover:underline">Changelog</button>
                </div>
            </div>
        </footer>
    </main>

    <!-- Modals -->
    <VisitModal isOpen={isScheduling} onClose={() => isScheduling = false} property={selectedProperty} {identityDoc} onSubmit={submitVisit} {isSubmitting} />
    <LoginModal isOpen={showLoginModal} onClose={() => showLoginModal = false} onLogin={login} onToggleRegister={() => { showLoginModal = false; showRegisterModal = true; }} {isSubmitting} error={loginError} />
    <RegisterModal isOpen={showRegisterModal} onClose={() => showRegisterModal = false} onRegister={register} onToggleLogin={() => { showRegisterModal = false; showLoginModal = true; }} {isSubmitting} error={loginError} />
    <OfferModal isOpen={showOfferModal} onClose={() => showOfferModal = false} property={selectedVisitForOffer?.property} onSubmit={(amt, trms) => submitOffer(selectedVisitForOffer.id, amt, trms)} {isSubmitting} />
    <PropertyModal isOpen={showPropertyModal} onClose={() => showPropertyModal = false} onSubmit={storeProperty} {isSubmitting} />
    <MaintenanceRequestModal isOpen={showMaintenanceModal} onClose={() => showMaintenanceModal = false} lease={selectedLeaseForMaintenance} onSubmit={submitMaintenanceRequest} {isSubmitting} />
</div>

<style>
    :global(.material-symbols-outlined) {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        vertical-align: middle;
    }
</style>
