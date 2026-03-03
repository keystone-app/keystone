<script>
    import { onMount } from 'svelte';
    import { Search, Home, FileText, Plus, LogIn, Filter, Calendar, Clock, Upload, CheckCircle2, X } from 'lucide-svelte';
    import Button from './components/ui/Button.svelte';
    import Badge from './components/ui/Badge.svelte';
    import PropertyCard from './components/features/PropertyCard.svelte';
    import VisitTable from './components/features/VisitTable.svelte';
    import OfferTable from './components/features/OfferTable.svelte';
    import OfferModal from './components/features/OfferModal.svelte';
    import VisitModal from './components/features/VisitModal.svelte';
    import LoginModal from './components/features/LoginModal.svelte';
    import RegisterModal from './components/features/RegisterModal.svelte';

    let role = $state('guest'); // 'landlord', 'tenant', 'guest'
    let view = $state('listings'); // 'listings', 'dashboard'
    let landlordView = $state('properties'); // 'properties', 'visits', 'offers'
    let tenantView = $state('visits'); // 'visits', 'offers', 'leases'
    let isLoggedIn = $state(false);
    let currentUser = $state(null);
    let landlordVisits = $state([]);
    let myVisits = $state([]);
    let offers = $state([]);

    async function fetchMyVisits() {
        try {
            const res = await fetch('/my-visits');
            if (res.ok) {
                myVisits = await res.json();
            }
        } catch (e) {
            console.error('Failed to fetch my visits', e);
        }
    }

    async function fetchOffers() {
        try {
            const res = await fetch('/offers');
            if (res.ok) {
                offers = await res.json();
            }
        } catch (e) {
            console.error('Failed to fetch offers', e);
        }
    }

    async function submitOffer(visitId, amount, terms) {
        try {
            const res = await fetch('/offers', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ visit_id: visitId, amount, terms })
            });

            if (res.ok) {
                const newOffer = await res.json();
                offers = [newOffer, ...offers];
                alert('Offer submitted successfully!');
                return true;
            } else {
                const data = await res.json();
                alert(data.message || 'Offer submission failed.');
                return false;
            }
        } catch (e) {
            alert('Connection error.');
            return false;
        }
    }

    async function updateOfferStatus(offerId, status, amount = null, terms = null) {
        try {
            const res = await fetch(`/offers/${offerId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status, amount, terms })
            });

            if (res.ok) {
                const updatedOffer = await res.json();
                offers = offers.map(o => o.id === offerId ? updatedOffer : o);
                if (status === 'accepted') {
                    alert('Offer accepted! Lease drafting initiated.');
                }
            }
        } catch (e) {
            alert('Failed to update offer.');
        }
    }

    async function fetchLandlordVisits() {
        try {
            const res = await fetch('/visits');
            if (res.ok) {
                landlordVisits = await res.json();
            }
        } catch (e) {
            console.error('Failed to fetch landlord visits', e);
        }
    }

    async function updateVisitStatus(visitId, newStatus) {
        try {
            const res = await fetch(`/visits/${visitId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: newStatus })
            });

            if (res.ok) {
                const updatedVisit = await res.json();
                landlordVisits = landlordVisits.map(v => v.id === visitId ? updatedVisit : v);
            }
        } catch (e) {
            alert('Failed to update visit status.');
        }
    }

    function approveVisit(id) {
        updateVisitStatus(id, 'scheduled');
    }

    function rejectVisit(id) {
        updateVisitStatus(id, 'rejected');
    }

    let searchQuery = $state('');
    let statusFilter = $state('all');
    let selectedProperty = $state(null);

    let isScheduling = $state(false);
    let showAuthPrompt = $state(false);
    let showLoginModal = $state(false);
    let showRegisterModal = $state(false);
    let identityDoc = $state(null);
    let visitDate = $state('');
    let visitTime = $state('');
    let schedulingStep = $state(1); // 1: DateTime, 2: Document

    let loginError = $state('');
    let isSubmitting = $state(false);

    let showOfferModal = $state(false);
    let selectedVisitForOffer = $state(null);

    function openOfferModal(visit) {
        selectedVisitForOffer = visit;
        showOfferModal = true;
    }

    async function handleOfferSubmission(amount, terms) {
        return await submitOffer(selectedVisitForOffer.id, amount, terms);
    }

    const properties = $state([
        { 
            id: 1, 
            name: 'Modern Apartment 101', 
            address: '123 Legal Lane, Suite 101', 
            price: 2500, 
            status: 'available', 
            type: 'Apartment',
            description: 'A stunning modern apartment featuring floor-to-ceiling windows, an open-concept kitchen, and premium finishes throughout. Located in the heart of the legal district.',
            features: ['2 Bedrooms', '2 Bathrooms', 'Parking Included', 'Gym Access'],
            compliance: { gas: 'Verified', fire: 'Verified', electric: 'Pending' }
        },
        { 
            id: 2, 
            name: 'Cozy Studio Downtown', 
            address: '456 Urban Ave, #4B', 
            price: 1800, 
            status: 'rented', 
            type: 'Studio',
            description: 'Efficient and stylish studio apartment perfect for young professionals. Close to public transport and local amenities.',
            features: ['Studio', '1 Bathroom', 'High Ceilings', 'Pet Friendly'],
            compliance: { gas: 'Verified', fire: 'Verified', electric: 'Verified' }
        },
        { 
            id: 3, 
            name: 'Spacious Family Home', 
            address: '789 Suburban Way', 
            price: 3500, 
            status: 'available', 
            type: 'House',
            description: 'Large family home with a beautiful garden, modern appliances, and a quiet neighborhood atmosphere.',
            features: ['4 Bedrooms', '3 Bathrooms', 'Large Garden', 'Double Garage'],
            compliance: { gas: 'Verified', fire: 'Verified', electric: 'Verified' }
        },
        { 
            id: 4, 
            name: 'Luxury Penthouse', 
            address: '1 Sky High Plaza', 
            price: 5000, 
            status: 'available', 
            type: 'Penthouse',
            description: 'Ultimate luxury living with panoramic city views, private elevator access, and a wraparound terrace.',
            features: ['3 Bedrooms', '3.5 Bathrooms', 'Private Terrace', 'Smart Home System'],
            compliance: { gas: 'Verified', fire: 'Verified', electric: 'Verified' }
        },
        { 
            id: 5, 
            name: 'Rustic Loft', 
            address: '22 Industrial Dr', 
            price: 2200, 
            status: 'maintenance', 
            type: 'Loft',
            description: 'Authentic industrial loft with exposed brick, timber beams, and an open layout. Currently undergoing premium renovations.',
            features: ['1 Bedroom', '1 Bathroom', 'Exposed Brick', 'High Ceilings'],
            compliance: { gas: 'Expired', fire: 'Verified', electric: 'Verified' }
        },
    ]);

    const filteredProperties = $derived(
        properties.filter(p => {
            const matchesSearch = p.name.toLowerCase().includes(searchQuery.toLowerCase()) || 
                                p.address.toLowerCase().includes(searchQuery.toLowerCase());
            const matchesStatus = statusFilter === 'all' || p.status === statusFilter;
            return matchesSearch && matchesStatus;
        })
    );

    onMount(async () => {
        console.log('App mounted, checking auth...');
        await checkAuth();
    });

    async function checkAuth() {
        try {
            const res = await fetch('/me');
            const data = await res.json();
            console.log('Auth check response:', data);
            if (data.user) {
                isLoggedIn = true;
                currentUser = data.user;
                role = data.role;
                identityDoc = data.identity_document;

                fetchOffers();
                fetchMyVisits();
                if (role === 'landlord') {
                    fetchLandlordVisits();
                }
            }
        } catch (e) {
            console.error('Auth check failed', e);
        }
    }

    function updateCsrfToken(newToken) {
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) meta.content = newToken;
    }

    async function register({ name, email, password }) {
        loginError = '';
        isSubmitting = true;
        try {
            const res = await fetch('/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ name, email, password })
            });

            if (res.ok) {
                const data = await res.json();
                isLoggedIn = true;
                currentUser = data.user;
                role = data.role;
                updateCsrfToken(data.csrf_token);
                showRegisterModal = false;
                showAuthPrompt = false;
                
                fetchOffers();
                fetchMyVisits();
                if (role === 'landlord') fetchLandlordVisits();

                if (isScheduling) {
                    schedulingStep = 1;
                }
            } else {
                const data = await res.json();
                loginError = data.message || 'Registration failed.';
            }
        } catch (e) {
            loginError = 'Connection error. Please try again.';
        } finally {
            isSubmitting = false;
        }
    }

    async function login({ email, password }) {
        loginError = '';
        isSubmitting = true;
        try {
            const res = await fetch('/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email, password })
            });

            if (res.ok) {
                const data = await res.json();
                isLoggedIn = true;
                currentUser = data.user;
                role = data.role;
                updateCsrfToken(data.csrf_token);
                showLoginModal = false;
                showAuthPrompt = false;
                
                fetchOffers();
                fetchMyVisits();
                if (role === 'landlord') fetchLandlordVisits();

                if (isScheduling) {
                    schedulingStep = 1;
                }
            } else {
                const data = await res.json();
                loginError = data.message || 'Login failed. Please check your credentials.';
            }
        } catch (e) {
            loginError = 'Connection error. Please try again.';
        } finally {
            isSubmitting = false;
        }
    }

    async function logout() {
        console.log('Logout initiated...');
        try {
            const res = await fetch('/logout', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            console.log('Logout response status:', res.status);
            window.location.reload();
        } catch (e) {
            console.error('Logout failed', e);
            // Fallback: reload anyway to clear state
            window.location.reload();
        }
    }

    function startScheduling() {
        isScheduling = true;
        schedulingStep = 1;
        if (!isLoggedIn) {
            showAuthPrompt = true;
        }
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
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            if (res.ok) {
                await fetchOffers();
                alert(`${type.replace('_', ' ')} uploaded successfully!`);
            } else {
                const data = await res.json();
                alert(data.message || 'Upload failed.');
            }
        } catch (e) {
            alert('Upload error.');
        } finally {
            isSubmitting = false;
        }
    }

    async function handleIncomeVerification(offerId) {
        isSubmitting = true;
        try {
            const res = await fetch(`/offers/${offerId}/verify`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                await fetchOffers();
                alert('Income verified via Open Finance!');
            }
        } catch (e) {
            alert('Verification failed.');
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
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (uploadRes.ok) {
                    const uploadedDoc = await uploadRes.json();
                    docId = uploadedDoc.id;
                    identityDoc = uploadedDoc;
                } else {
                    const data = await uploadRes.json();
                    throw new Error(data.message || 'Identity upload failed.');
                }
            }

            const visitRes = await fetch('/visits', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    property_id: selectedProperty.id,
                    document_id: docId,
                    visit_at: `${date} ${time}`
                })
            });

            if (visitRes.ok) {
                const newVisit = await visitRes.json();
                myVisits = [...myVisits, { ...newVisit, property: selectedProperty }];
                return true;
            } else {
                const data = await visitRes.json();
                alert(data.message || 'Scheduling failed.');
                return false;
            }
        } catch (e) {
            alert(e.message || 'Connection error.');
            return false;
        } finally {
            isSubmitting = false;
        }
    }

    function viewDetails(property) {
        selectedProperty = property;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>

<div class="min-h-screen bg-gray-50 text-gray-900 font-sans antialiased selection:bg-indigo-100">
    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-50">
        <div class="flex items-center gap-8">
            <div class="flex items-center gap-2 cursor-pointer" onclick={() => { view = 'listings'; selectedProperty = null; }}>
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold">K</div>
                <span class="text-xl font-bold tracking-tight">Keystone</span>
            </div>
            
            <div class="hidden md:flex items-center gap-6">
                <button 
                    class="text-sm font-semibold {(view === 'listings' && !selectedProperty) ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-900'}"
                    onclick={() => { view = 'listings'; selectedProperty = null; }}
                >
                    Browse Properties
                </button>
                {#if isLoggedIn}
                    <button 
                        class="text-sm font-semibold {view === 'dashboard' ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-900'}"
                        onclick={() => view = 'dashboard'}
                    >
                        My Dashboard
                    </button>
                {/if}
            </div>
        </div>

        <div class="flex items-center gap-6">
            {#if isLoggedIn && currentUser}
                <span class="text-sm font-bold text-gray-500">Hi, {currentUser.name}</span>
                <div class="flex bg-gray-100 p-1 rounded-md">
                    <button 
                        class="px-4 py-1.5 text-sm font-medium rounded {role === 'landlord' ? 'bg-white shadow-sm text-indigo-600' : 'text-gray-500 hover:text-gray-700'}"
                        onclick={() => role = 'landlord'}
                    >
                        Landlord
                    </button>
                    <button 
                        class="px-4 py-1.5 text-sm font-medium rounded {role === 'tenant' ? 'bg-white shadow-sm text-indigo-600' : 'text-gray-500 hover:text-gray-700'}"
                        onclick={() => role = 'tenant'}
                    >
                        Tenant
                    </button>
                </div>
                <div class="h-8 w-px bg-gray-200"></div>
                <button 
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-bold transition-all shadow-lg shadow-red-100" 
                    onclick={logout}
                >
                    Log Out
                </button>
            {:else}
                <Button variant="primary" size="md" onclick={() => showLoginModal = true}>
                    <LogIn size={18} />
                    Sign In
                </Button>
            {/if}
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-8">
        {#if view === 'listings'}
            {#if selectedProperty}
                <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <button 
                        class="flex items-center gap-2 text-gray-500 hover:text-indigo-600 font-bold transition-colors mb-4"
                        onclick={() => selectedProperty = null}
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                        Back to Listings
                    </button>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                        <div class="lg:col-span-2 space-y-8">
                            <div class="h-[400px] bg-gray-100 rounded-3xl flex items-center justify-center border border-gray-200 shadow-inner">
                                <Home class="w-32 h-32 text-gray-300" />
                            </div>
                            
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <Badge type="primary">{selectedProperty.type}</Badge>
                                    <Badge type={selectedProperty.status === 'available' ? 'success' : selectedProperty.status === 'rented' ? 'info' : 'warning'}>
                                        {selectedProperty.status}
                                    </Badge>
                                </div>
                                <h1 class="text-5xl font-black tracking-tight">{selectedProperty.name}</h1>
                                <p class="text-xl text-gray-500 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                    {selectedProperty.address}
                                </p>
                            </div>

                            <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm space-y-6">
                                <h2 class="text-2xl font-bold">About this property</h2>
                                <p class="text-gray-600 leading-relaxed text-lg">
                                    {selectedProperty.description}
                                </p>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    {#each selectedProperty.features as feature}
                                        <div class="bg-gray-50 p-4 rounded-2xl flex flex-col items-center text-center gap-2">
                                            <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Feature</span>
                                            <span class="font-bold text-sm">{feature}</span>
                                        </div>
                                    {/each}
                                </div>
                            </div>
                        </div>

                        <div class="space-y-8">
                            <div class="bg-indigo-600 p-8 rounded-3xl text-white shadow-xl shadow-indigo-100 space-y-6 sticky top-28">
                                <div class="space-y-1">
                                    <p class="text-indigo-200 text-sm font-black uppercase tracking-widest">Monthly Rent</p>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-5xl font-black">${selectedProperty.price.toLocaleString()}</span>
                                        <span class="text-indigo-200 font-bold">/mo</span>
                                    </div>
                                </div>

                                <div class="space-y-4 pt-6 border-t border-indigo-500">
                                    <h3 class="font-bold text-lg flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                        Legal Compliance
                                    </h3>
                                    <div class="space-y-3">
                                        {#each Object.entries(selectedProperty.compliance) as [cert, status]}
                                            <div class="flex justify-between items-center bg-indigo-700/50 p-3 rounded-xl">
                                                <span class="text-sm font-bold capitalize">{cert} Certificate</span>
                                                <Badge type={status === 'Verified' ? 'success' : status === 'Pending' ? 'warning' : 'error'}>
                                                    {status}
                                                </Badge>
                                            </div>
                                        {/each}
                                    </div>
                                </div>

                                <Button variant="secondary" size="xl" class="w-full bg-white text-indigo-600 hover:bg-indigo-50" onclick={startScheduling}>
                                    Schedule a Visit
                                </Button>
                                <p class="text-center text-xs text-indigo-200 font-bold">Secure through Keystone Legal Framework</p>
                            </div>
                        </div>
                    </div>
                </div>
            {:else}
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
                            <PropertyCard {property} onViewDetails={viewDetails} />
                        {/each}
                    </div>

                    {#if filteredProperties.length === 0}
                        <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-gray-200">
                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <Search class="text-gray-300" size={40} />
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">No properties found</h3>
                            <p class="text-gray-500 mt-1">Try adjusting your filters or search query.</p>
                            <Button variant="ghost" class="mt-6 text-indigo-600 font-bold hover:underline" onclick={() => { searchQuery = ''; statusFilter = 'all'; }}>
                                Clear all filters
                            </Button>
                        </div>
                    {/if}
                </div>
            {/if}
        {:else if isLoggedIn}
            {#if role === 'landlord'}
                <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <header class="flex flex-col gap-6">
                        <div class="flex justify-between items-end">
                            <div>
                                <h1 class="text-3xl font-black">{landlordView === 'properties' ? 'Properties' : 'Visit Requests'}</h1>
                                <p class="text-gray-500 mt-1">
                                    {landlordView === 'properties' ? 'Manage your real estate portfolio' : 'Review and approve tenant visits'}
                                </p>
                            </div>
                            {#if landlordView === 'properties'}
                                <Button variant="primary" size="lg" class="shadow-lg shadow-indigo-200">
                                    <Plus size={20} />
                                    Add Property
                                </Button>
                            {/if}
                        </div>

                        <!-- Landlord Menu -->
                        <div class="flex border-b border-gray-200">
                            <Button variant="tab" class={landlordView === 'properties' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-400'} onclick={() => landlordView = 'properties'}>
                                My Portfolio
                            </Button>
                            <Button variant="tab" class={landlordView === 'visits' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-400'} onclick={() => landlordView = 'visits'}>
                                Visit Requests
                                {#if landlordVisits.filter(v => v.status === 'pending').length > 0}
                                    <Badge type="info" class="ml-2 px-2 py-0.5">
                                        {landlordVisits.filter(v => v.status === 'pending').length}
                                    </Badge>
                                {/if}
                            </Button>
                            <Button variant="tab" class={landlordView === 'offers' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-400'} onclick={() => landlordView = 'offers'}>
                                Offers
                                {#if offers.filter(o => o.status === 'pending').length > 0}
                                    <Badge type="info" class="ml-2 px-2 py-0.5">
                                        {offers.filter(o => o.status === 'pending').length}
                                    </Badge>
                                {/if}
                            </Button>
                        </div>
                    </header>

                    {#if landlordView === 'properties'}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {#each properties.filter(p => p.id <= 3) as property}
                                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                    <div class="h-48 bg-gray-50 flex items-center justify-center border-b border-gray-50">
                                        <Home class="w-12 h-12 text-gray-200" />
                                    </div>
                                    <div class="p-5">
                                        <div class="flex justify-between items-start mb-2">
                                            <h3 class="font-bold text-lg">{property.name}</h3>
                                            <Badge type="success">Occupied</Badge>
                                        </div>
                                        <p class="text-gray-500 text-sm mb-4">{property.address}</p>
                                        <div class="flex justify-between items-center pt-4 border-t border-gray-50">
                                            <span class="font-black text-indigo-600">${property.price.toLocaleString()}/mo</span>
                                            <Button variant="ghost" class="text-indigo-600 hover:text-indigo-800 text-sm font-bold">Manage</Button>
                                        </div>
                                    </div>
                                </div>
                            {/each}
                        </div>
                    {:else if landlordView === 'visits'}
                        <VisitTable visits={landlordVisits} role="landlord" onApprove={approveVisit} onReject={rejectVisit} onViewId={(v) => alert('Viewing ID: ' + v.document.name)} />
                    {:else if landlordView === 'offers'}
                        <OfferTable offers={offers} role="landlord" onUpdateStatus={updateOfferStatus} />
                    {/if}
                </div>
            {:else}
                <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <header class="flex flex-col gap-6">
                        <div class="space-y-1">
                            <h1 class="text-3xl font-black">
                                {#if tenantView === 'visits'}My Scheduled Visits
                                {:else}My Dashboard
                                {/if}
                            </h1>
                            <p class="text-gray-500 mt-1">
                                {#if tenantView === 'visits'}Track and manage your upcoming property visits
                                {:else if tenantView === 'offers'}Negotiate and track your property offers
                                {:else}Manage your active lease agreements
                                {/if}
                            </p>
                        </div>

                        <!-- Tenant/Guest Menu -->
                        <div class="flex border-b border-gray-200">
                            <Button variant="tab" class={tenantView === 'visits' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-400'} onclick={() => tenantView = 'visits'}>
                                Scheduled Visits
                                {#if myVisits.length > 0}
                                    <Badge type="info" class="ml-2 px-2 py-0.5">{myVisits.length}</Badge>
                                {/if}
                            </Button>
                            <Button variant="tab" class={tenantView === 'offers' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-400'} onclick={() => tenantView = 'offers'}>
                                Negotiations
                                {#if offers.length > 0}
                                    <Badge type="info" class="ml-2 px-2 py-0.5">{offers.length}</Badge>
                                {/if}
                            </Button>
                            <Button variant="tab" class={tenantView === 'leases' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-400'} onclick={() => tenantView = 'leases'}>
                                My Leases
                            </Button>
                        </div>
                    </header>

                    {#if tenantView === 'leases'}
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
                                <Button variant="secondary" size="sm">Download PDF</Button>
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
                                    <Badge type="success">Verified</Badge>
                                </div>
                                {#if identityDoc}
                                    <div class="bg-white border border-indigo-200 rounded-2xl p-6 flex items-center justify-between shadow-sm border-l-4 border-l-indigo-600">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600">
                                                <CheckCircle2 size={20} />
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-sm text-indigo-900">{identityDoc.name}</h4>
                                                <p class="text-gray-400 text-[10px] font-bold mt-0.5">Primary Identity Document</p>
                                            </div>
                                        </div>
                                        <Badge type="info">Verified</Badge>
                                    </div>
                                {/if}
                            </div>
                        </section>
                    {:else if tenantView === 'visits'}
                        <VisitTable visits={myVisits} onMakeOffer={openOfferModal} />
                    {:else if tenantView === 'offers'}
                        <OfferTable offers={offers} onUploadCompliance={handleComplianceUpload} onVerifyIncome={handleIncomeVerification} />
                    {/if}
                </div>
            {/if}
        {/if}

        <VisitModal 
            isOpen={isScheduling} 
            onClose={() => { isScheduling = false; showAuthPrompt = false; }}
            property={selectedProperty}
            identityDoc={identityDoc}
            onSubmit={submitVisit}
            isSubmitting={isSubmitting}
        />

        <LoginModal 
            isOpen={showLoginModal} 
            onClose={() => showLoginModal = false}
            onLogin={login}
            onToggleRegister={() => { showLoginModal = false; showRegisterModal = true; }}
            isSubmitting={isSubmitting}
            error={loginError}
        />

        <RegisterModal 
            isOpen={showRegisterModal} 
            onClose={() => showRegisterModal = false}
            onRegister={register}
            onToggleLogin={() => { showRegisterModal = false; showLoginModal = true; }}
            isSubmitting={isSubmitting}
            error={loginError}
        />

        <OfferModal 
            isOpen={showOfferModal} 
            onClose={() => showOfferModal = false}
            property={selectedVisitForOffer?.property}
            onSubmit={handleOfferSubmission}
            isSubmitting={isSubmitting}
        />

        <!-- Authentication Prompt Modal -->
        {#if showAuthPrompt}
            <div class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm animate-in fade-in duration-300">
                <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden animate-in zoom-in-95 duration-300">
                    <div class="p-8 text-center space-y-6">
                        <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto text-indigo-600">
                            <LogIn size={40} />
                        </div>
                        <div class="space-y-2">
                            <h2 class="text-3xl font-black">Authentication Required</h2>
                            <p class="text-gray-500">To ensure safety and legal compliance, you must be signed in to schedule a visit.</p>
                        </div>
                        <div class="flex flex-col gap-3">
                            <Button variant="primary" size="xl" onclick={() => { showAuthPrompt = false; showLoginModal = true; }}>
                                Sign In or Create Account
                            </Button>
                            <Button variant="secondary" size="xl" onclick={() => { showAuthPrompt = false; isScheduling = false; }}>
                                Not now
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        {/if}
    </main>
</div>

<style>
</style>
