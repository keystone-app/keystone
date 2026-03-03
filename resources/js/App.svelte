<script>
    import { onMount } from 'svelte';
    import { Search, Home, FileText, Plus, LogIn, Filter, Calendar, Clock, Upload, CheckCircle2, X } from 'lucide-svelte';

    let role = $state('guest'); // 'landlord', 'tenant', 'guest'
    let view = $state('listings'); // 'listings', 'dashboard'
    let landlordView = $state('properties'); // 'properties', 'visits', 'offers'
    let isLoggedIn = $state(false);
    let currentUser = $state(null);
    let landlordVisits = $state([]);
    let offers = $state([]);

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
    let selectedFile = $state(null);
    let filePreviewUrl = $state('');
    let hasUploadedIdentityDoc = $derived(!!identityDoc || !!selectedFile);
    let visitDate = $state('');
    let visitTime = $state('');
    let schedulingStep = $state(1); // 1: DateTime, 2: Document

    let email = $state('alice@landlord.com'); // Default for quick testing
    let password = $state('password');
    let regName = $state('');
    let loginError = $state('');
    let isSubmitting = $state(false);

    let showOfferModal = $state(false);
    let offerAmount = $state('');
    let offerTerms = $state('');
    let selectedVisitForOffer = $state(null);

    function openOfferModal(visit) {
        selectedVisitForOffer = visit;
        offerAmount = visit.property.price;
        offerTerms = 'Standard legal terms as per Keystone framework.';
        showOfferModal = true;
    }

    async function handleOfferSubmission() {
        if (await submitOffer(selectedVisitForOffer.id, offerAmount, offerTerms)) {
            showOfferModal = false;
        }
    }

    let myVisits = $state([]);

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
        await checkAuth();
    });

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

    async function register() {
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
                body: JSON.stringify({ name: regName, email, password })
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

    async function login() {
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
        try {
            await fetch('/logout', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            window.location.reload();
        } catch (e) {
            console.error('Logout failed', e);
        }
    }

    function startScheduling() {
        isScheduling = true;
        schedulingStep = 1;
        if (!isLoggedIn) {
            showAuthPrompt = true;
        }
    }

    function handleFileSelection(e) {
        const file = e.target.files[0];
        if (!file) return;

        if (filePreviewUrl) URL.revokeObjectURL(filePreviewUrl);
        
        selectedFile = file;
        filePreviewUrl = URL.createObjectURL(file);
    }

    function removeFile() {
        if (filePreviewUrl) URL.revokeObjectURL(filePreviewUrl);
        selectedFile = null;
        filePreviewUrl = '';
    }

    async function submitVisit() {
        if (!selectedFile && !identityDoc) {
            alert('Please select an identity document.');
            return;
        }

        isSubmitting = true;
        try {
            let docId = identityDoc?.id;

            // If a new file is selected, upload it first
            if (selectedFile) {
                const formData = new FormData();
                formData.append('file', selectedFile);

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
                    identityDoc = uploadedDoc; // Save for future visits
                    removeFile(); // Clear preview
                } else {
                    const data = await uploadRes.json();
                    throw new Error(data.message || 'Identity upload failed.');
                }
            }

            // Create the visit request
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
                    visit_at: `${visitDate} ${visitTime}`
                })
            });

            if (visitRes.ok) {
                const newVisit = await visitRes.json();
                myVisits = [...myVisits, { ...newVisit, property: selectedProperty }];
                isScheduling = false;
                alert('Visit scheduled! Status: Pending landlord approval.');
            } else {
                const data = await visitRes.json();
                alert(data.message || 'Scheduling failed.');
            }
        } catch (e) {
            alert(e.message || 'Connection error. Please try again.');
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
            <div class="flex items-center gap-2 cursor-pointer" on:click={() => { view = 'listings'; selectedProperty = null; }}>
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold">K</div>
                <span class="text-xl font-bold tracking-tight">Keystone</span>
            </div>
            
            <div class="hidden md:flex items-center gap-6">
                <button 
                    class="text-sm font-semibold {(view === 'listings' && !selectedProperty) ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-900'}"
                    on:click={() => { view = 'listings'; selectedProperty = null; }}
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
                <span class="text-sm font-bold text-gray-500">Hi, {currentUser.name}</span>
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
                <button class="text-gray-500 hover:text-gray-700 font-medium" on:click={logout}>Log Out</button>
            {:else}
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold flex items-center gap-2 transition-colors" on:click={() => showLoginModal = true}>
                    <LogIn size={18} />
                    Sign In
                </button>
            {/if}
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-8">
        {#if view === 'listings'}
            {#if selectedProperty}
                <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <button 
                        class="flex items-center gap-2 text-gray-500 hover:text-indigo-600 font-bold transition-colors mb-4"
                        on:click={() => selectedProperty = null}
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
                                    <span class="bg-indigo-600 text-white text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider">{selectedProperty.type}</span>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold shadow-sm 
                                        {selectedProperty.status === 'available' ? 'bg-green-100 text-green-700' : 
                                         selectedProperty.status === 'rented' ? 'bg-indigo-100 text-indigo-700' : 'bg-amber-100 text-amber-700'}">
                                        {selectedProperty.status.charAt(0).toUpperCase() + selectedProperty.status.slice(1)}
                                    </span>
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
                                                <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-md 
                                                    {status === 'Verified' ? 'bg-green-400 text-green-900' : 
                                                     status === 'Pending' ? 'bg-amber-400 text-amber-900' : 'bg-red-400 text-red-900'}">
                                                    {status}
                                                </span>
                                            </div>
                                        {/each}
                                    </div>
                                </div>

                                <button 
                                    class="w-full bg-white text-indigo-600 hover:bg-indigo-50 py-4 rounded-2xl font-black transition-all shadow-lg text-lg"
                                    on:click={startScheduling}
                                >
                                    Schedule a Visit
                                </button>
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
                                        <button 
                                            class="bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white px-4 py-2 rounded-xl text-sm font-bold transition-all"
                                            on:click={() => viewDetails(property)}
                                        >
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
                                <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2 shadow-lg shadow-indigo-200">
                                    <Plus size={20} />
                                    Add Property
                                </button>
                            {/if}
                        </div>

                        <!-- Landlord Menu -->
                        <div class="flex border-b border-gray-200">
                            <button 
                                class="px-6 py-3 text-sm font-bold border-b-2 transition-all {landlordView === 'properties' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-400 hover:text-gray-600'}"
                                on:click={() => landlordView = 'properties'}
                            >
                                My Portfolio
                            </button>
                            <button 
                                class="px-6 py-3 text-sm font-bold border-b-2 transition-all {landlordView === 'visits' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-400 hover:text-gray-600'}"
                                on:click={() => landlordView = 'visits'}
                            >
                                Visit Requests
                                {#if landlordVisits.filter(v => v.status === 'pending').length > 0}
                                    <span class="ml-2 bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded-full text-[10px]">
                                        {landlordVisits.filter(v => v.status === 'pending').length}
                                    </span>
                                {/if}
                            </button>
                            <button 
                                class="px-6 py-3 text-sm font-bold border-b-2 transition-all {landlordView === 'offers' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-400 hover:text-gray-600'}"
                                on:click={() => landlordView = 'offers'}
                            >
                                Offers
                                {#if offers.filter(o => o.status === 'pending').length > 0}
                                    <span class="ml-2 bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded-full text-[10px]">
                                        {offers.filter(o => o.status === 'pending').length}
                                    </span>
                                {/if}
                            </button>
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
                    {:else}
                        <div class="bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-gray-50 border-b border-gray-100">
                                    <tr>
                                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Tenant</th>
                                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Property</th>
                                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Schedule</th>
                                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Identity</th>
                                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Status</th>
                                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    {#each landlordVisits as visit}
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-6 py-5 font-bold text-sm">{visit.user.name}</td>
                                            <td class="px-6 py-5 text-sm text-gray-600">{visit.property.name}</td>
                                            <td class="px-6 py-5 text-sm">
                                                <div class="flex flex-col">
                                                    <span class="font-bold">{new Date(visit.visit_at).toLocaleDateString()}</span>
                                                    <span class="text-xs text-gray-400 font-medium">{new Date(visit.visit_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-5">
                                                <button 
                                                    class="text-indigo-600 hover:text-indigo-800 text-xs font-bold flex items-center gap-1 group"
                                                    on:click={() => alert('Viewing document: ' + visit.document.name)}
                                                >
                                                    <FileText size={14} class="text-indigo-400 group-hover:text-indigo-600" />
                                                    View ID
                                                </button>
                                            </td>
                                            <td class="px-6 py-5">
                                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider
                                                    {visit.status === 'pending' ? 'bg-amber-100 text-amber-700' : 
                                                     visit.status === 'scheduled' ? 'bg-green-100 text-green-700' : 
                                                     'bg-red-100 text-red-700'}">
                                                    {visit.status}
                                                </span>
                                            </td>
                                            <td class="px-6 py-5 text-right">
                                                {#if visit.status === 'pending'}
                                                    <div class="flex justify-end gap-2">
                                                        <button 
                                                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-[10px] font-black uppercase transition-all shadow-sm shadow-indigo-100"
                                                            on:click={() => approveVisit(visit.id)}
                                                        >
                                                            Approve
                                                        </button>
                                                        <button 
                                                            class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase transition-all"
                                                            on:click={() => rejectVisit(visit.id)}
                                                        >
                                                            Reject
                                                        </button>
                                                    </div>
                                                {:else}
                                                    <span class="text-xs text-gray-400 font-bold italic">Resolved</span>
                                                {/if}
                                            </td>
                                        </tr>
                                    {/each}
                                </tbody>
                            </table>
                            {#if landlordVisits.length === 0}
                                <div class="p-20 text-center space-y-4">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto text-gray-300">
                                        <Calendar size={32} />
                                    </div>
                                    <p class="text-gray-500 font-medium">No visit requests at the moment.</p>
                                </div>
                            {/if}
                        </div>
                    {:else if landlordView === 'offers'}
                        <div class="bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-gray-50 border-b border-gray-100">
                                    <tr>
                                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Tenant</th>
                                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Property</th>
                                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Offer Amount</th>
                                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Status</th>
                                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    {#each offers as offer}
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-6 py-5">
                                                <div class="flex flex-col">
                                                    <span class="font-bold text-sm">{offer.user.name}</span>
                                                    <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Verified Identity</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-5 text-sm text-gray-600">{offer.property.name}</td>
                                            <td class="px-6 py-5">
                                                <div class="flex flex-col">
                                                    <span class="text-indigo-600 font-black">${parseFloat(offer.amount).toLocaleString()}/mo</span>
                                                    <span class="text-[10px] text-gray-400 truncate max-w-[150px]">{offer.terms}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-5">
                                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider
                                                    {offer.status === 'pending' ? 'bg-amber-100 text-amber-700' : 
                                                     offer.status === 'accepted' ? 'bg-green-100 text-green-700' : 
                                                     'bg-red-100 text-red-700'}">
                                                    {offer.status}
                                                </span>
                                            </td>
                                            <td class="px-6 py-5 text-right">
                                                {#if offer.status === 'pending'}
                                                    <div class="flex justify-end gap-2">
                                                        <button 
                                                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-[10px] font-black uppercase transition-all shadow-sm shadow-indigo-100"
                                                            on:click={() => updateOfferStatus(offer.id, 'accepted')}
                                                        >
                                                            Accept
                                                        </button>
                                                        <button 
                                                            class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase transition-all"
                                                            on:click={() => updateOfferStatus(offer.id, 'countered')}
                                                        >
                                                            Counter
                                                        </button>
                                                        <button 
                                                            class="bg-white border border-red-100 hover:bg-red-50 text-red-600 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase transition-all"
                                                            on:click={() => updateOfferStatus(offer.id, 'rejected')}
                                                        >
                                                            Reject
                                                        </button>
                                                    </div>
                                                {:else}
                                                    <span class="text-xs text-gray-400 font-bold italic">Negotiation Ended</span>
                                                {/if}
                                            </td>
                                        </tr>
                                    {/each}
                                </tbody>
                            </table>
                            {#if offers.length === 0}
                                <div class="p-20 text-center space-y-4">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto text-gray-300">
                                        <FileText size={32} />
                                    </div>
                                    <p class="text-gray-500 font-medium">No offers received yet.</p>
                                </div>
                            {/if}
                        </div>
                    {/if}
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
                            {#if hasUploadedIdentityDoc}
                                <div class="bg-white border border-indigo-200 rounded-2xl p-6 flex items-center justify-between shadow-sm border-l-4 border-l-indigo-600">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600">
                                            <CheckCircle2 size={20} />
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-sm text-indigo-900">Legal_ID_Card.png</h4>
                                            <p class="text-gray-400 text-[10px] font-bold mt-0.5">Primary Identity Document</p>
                                        </div>
                                    </div>
                                    <span class="text-indigo-600 font-bold text-[10px] bg-indigo-50 px-2 py-1 rounded-md uppercase tracking-wider">Verified</span>
                                </div>
                            {/if}
                        </div>
                    </section>

                    {#if myVisits.length > 0}
                        <section class="space-y-4">
                            <h2 class="text-xl font-bold">Upcoming Visits</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {#each myVisits as visit}
                                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex items-center justify-between">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400">
                                                <Calendar size={24} />
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-lg">{visit.property.name}</h4>
                                                <div class="flex items-center gap-3 text-sm text-gray-500">
                                                    <span class="flex items-center gap-1"><Calendar size={14} /> {visit.date}</span>
                                                    <span class="flex items-center gap-1"><Clock size={14} /> {visit.time}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider
                                            {visit.status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700'}">
                                            {visit.status}
                                        </span>
                                        {#if visit.status === 'scheduled'}
                                            <button 
                                                class="ml-4 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-sm shadow-indigo-100"
                                                on:click={() => openOfferModal(visit)}
                                            >
                                                Make Offer
                                            </button>
                                        {/if}
                                    </div>
                                {/each}
                            </div>
                        </section>
                    {/if}

                    {#if offers.length > 0}
                        <section class="space-y-4">
                            <h2 class="text-xl font-bold">My Offers</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {#each offers as offer}
                                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex flex-col gap-4">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="font-bold text-lg">{offer.property.name}</h4>
                                                <p class="text-sm font-black text-indigo-600">${parseFloat(offer.amount).toLocaleString()}/mo</p>
                                            </div>
                                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider
                                                {offer.status === 'pending' ? 'bg-amber-100 text-amber-700' : 
                                                 offer.status === 'accepted' ? 'bg-green-100 text-green-700' : 
                                                 'bg-red-100 text-red-700'}">
                                                {offer.status}
                                            </span>
                                        </div>
                                        {#if offer.status === 'accepted'}
                                            <div class="bg-green-50 p-3 rounded-xl flex items-center gap-3">
                                                <FileText class="text-green-600" size={20} />
                                                <div>
                                                    <p class="text-xs font-bold text-green-800">Lease Drafted</p>
                                                    <p class="text-[10px] text-green-600">Waiting for signatures</p>
                                                </div>
                                            </div>
                                        {/if}
                                    </div>
                                {/each}
                            </div>
                        </section>
                    {/if}
                </div>
            {/if}
        {/if}

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
                            <button 
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-black shadow-lg shadow-indigo-200 transition-all"
                                on:click={() => { showAuthPrompt = false; showLoginModal = true; }}
                            >
                                Sign In or Create Account
                            </button>
                            <button 
                                class="w-full bg-gray-100 hover:bg-gray-200 text-gray-600 py-4 rounded-2xl font-bold transition-all"
                                on:click={() => { showAuthPrompt = false; isScheduling = false; }}
                            >
                                Not now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        {/if}

        <!-- Scheduling Modal -->
        {#if isScheduling}
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm animate-in fade-in duration-300">
                <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden animate-in zoom-in-95 duration-300">
                    <div class="bg-indigo-600 p-6 text-white flex justify-between items-center">
                        <h2 class="text-xl font-black uppercase tracking-tight">Schedule Your Visit</h2>
                        <button class="hover:bg-indigo-500 p-1 rounded-lg transition-colors" on:click={() => isScheduling = false}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-8 space-y-8">
                        <!-- Progress Steps -->
                        <div class="flex items-center gap-4">
                            <div class="flex-1 h-2 rounded-full {schedulingStep >= 1 ? 'bg-indigo-600' : 'bg-gray-100'}"></div>
                            <div class="flex-1 h-2 rounded-full {schedulingStep >= 2 || hasUploadedIdentityDoc ? 'bg-indigo-600' : 'bg-gray-100'}"></div>
                        </div>

                        {#if schedulingStep === 1}
                            <div class="space-y-6 animate-in slide-in-from-right-4 duration-300">
                                <div class="space-y-2">
                                    <h3 class="text-2xl font-black">When would you like to visit?</h3>
                                    <p class="text-gray-500 text-sm">Select a date and time that works for you.</p>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="text-xs font-black uppercase tracking-widest text-gray-400">Date</label>
                                        <div class="relative">
                                            <Calendar class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" size={18} />
                                            <input type="date" bind:value={visitDate} class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold" />
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-xs font-black uppercase tracking-widest text-gray-400">Time</label>
                                        <div class="relative">
                                            <Clock class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" size={18} />
                                            <input type="time" bind:value={visitTime} class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold" />
                                        </div>
                                    </div>
                                </div>
                                <button 
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-black shadow-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                                    disabled={!visitDate || !visitTime}
                                    on:click={() => hasUploadedIdentityDoc ? submitVisit() : schedulingStep = 2}
                                >
                                    {hasUploadedIdentityDoc ? 'Confirm Visit' : 'Next: Verify Identity'}
                                </button>
                            </div>
                        {:else}
                            <div class="space-y-6 animate-in slide-in-from-right-4 duration-300">
                                <div class="space-y-2">
                                    <h3 class="text-2xl font-black">Identity Verification</h3>
                                    <p class="text-gray-500 text-sm">Please upload a valid legal document (Passport or Driver License) to secure your visit.</p>
                                </div>
                                
                                <div class="relative group">
                                    {#if filePreviewUrl}
                                        <div class="relative rounded-3xl overflow-hidden border-2 border-indigo-600 shadow-xl animate-in zoom-in-95 duration-300">
                                            <img src={filePreviewUrl} alt="Preview" class="w-full h-64 object-cover" />
                                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button 
                                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl font-bold flex items-center gap-2 transition-all shadow-lg"
                                                    on:click|preventDefault={removeFile}
                                                >
                                                    <X size={18} />
                                                    Remove Image
                                                </button>
                                            </div>
                                        </div>
                                    {:else}
                                        <label class="border-2 border-dashed border-gray-200 rounded-3xl p-10 flex flex-col items-center justify-center text-center gap-4 bg-gray-50/50 hover:border-indigo-300 transition-colors cursor-pointer group">
                                            <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center text-gray-400 group-hover:text-indigo-600 transition-colors">
                                                {#if identityDoc}
                                                    <CheckCircle2 size={32} class="text-green-500" />
                                                {:else}
                                                    <Upload size={32} />
                                                {/if}
                                            </div>
                                            <div class="space-y-1">
                                                {#if identityDoc}
                                                    <p class="font-bold text-green-600">Verified ID on file: {identityDoc.name}</p>
                                                    <p class="text-xs text-gray-400 font-medium">Click to upload a new one</p>
                                                {:else}
                                                    <p class="font-bold">Click to upload or drag & drop</p>
                                                    <p class="text-xs text-gray-400 font-medium">PNG, JPG or WEBP (max. 10MB)</p>
                                                {/if}
                                            </div>
                                            <input type="file" class="hidden" accept="image/*" on:change={handleFileSelection} />
                                        </label>
                                    {/if}
                                </div>

                                <div class="flex gap-3">
                                    <button 
                                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-600 py-4 rounded-2xl font-bold transition-all"
                                        on:click={() => schedulingStep = 1}
                                    >
                                        Back
                                    </button>
                                    <button 
                                        class="flex-[2] bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-black shadow-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2"
                                        on:click={submitVisit}
                                        disabled={!hasUploadedIdentityDoc || isSubmitting}
                                    >
                                        {#if isSubmitting}
                                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Processing...
                                        {:else}
                                            Complete Scheduling
                                        {/if}
                                    </button>
                                </div>
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        {/if}

        <!-- Login Modal -->
        {#if showLoginModal}
            <div class="fixed inset-0 z-[120] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-md animate-in fade-in duration-300">
                <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden animate-in zoom-in-95 duration-300 relative">
                    <button 
                        class="absolute top-4 right-4 p-2 hover:bg-gray-100 rounded-full transition-colors"
                        on:click={() => { showLoginModal = false; loginError = ''; }}
                    >
                        <X size={20} class="text-gray-400" />
                    </button>

                    <div class="p-10 space-y-8">
                        <div class="text-center space-y-2">
                            <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto text-white shadow-lg shadow-indigo-200 mb-6">
                                <LogIn size={32} />
                            </div>
                            <h2 class="text-3xl font-black">Welcome back</h2>
                            <p class="text-gray-500">Sign in to access your Keystone dashboard</p>
                        </div>

                        {#if loginError}
                            <div class="bg-red-50 border border-red-100 p-4 rounded-xl flex items-center gap-3 animate-in shake-in duration-300">
                                <X size={20} class="text-red-600" />
                                <p class="text-sm font-bold text-red-700">{loginError}</p>
                            </div>
                        {/if}

                        <form class="space-y-4" on:submit|preventDefault={login}>
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
                                />
                            </div>
                            <button 
                                type="submit"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-black shadow-lg shadow-indigo-200 transition-all flex items-center justify-center gap-2"
                                disabled={isSubmitting}
                            >
                                {#if isSubmitting}
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Signing in...
                                {:else}
                                    Sign In
                                {/if}
                            </button>
                        </form>

                        <div class="text-center">
                            <p class="text-sm text-gray-400 font-bold">Don't have an account? <button class="text-indigo-600 hover:underline" on:click={() => { showLoginModal = false; showRegisterModal = true; }}>Create one</button></p>
                        </div>
                    </div>
                </div>
            </div>
        {/if}

        <!-- Register Modal -->
        {#if showRegisterModal}
            <div class="fixed inset-0 z-[120] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-md animate-in fade-in duration-300">
                <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden animate-in zoom-in-95 duration-300 relative">
                    <button 
                        class="absolute top-4 right-4 p-2 hover:bg-gray-100 rounded-full transition-colors"
                        on:click={() => { showRegisterModal = false; loginError = ''; }}
                    >
                        <X size={20} class="text-gray-400" />
                    </button>

                    <div class="p-10 space-y-8">
                        <div class="text-center space-y-2">
                            <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto text-white shadow-lg shadow-indigo-200 mb-6">
                                <Plus size={32} />
                            </div>
                            <h2 class="text-3xl font-black">Create Account</h2>
                            <p class="text-gray-500">Join Keystone to schedule your visits</p>
                        </div>

                        {#if loginError}
                            <div class="bg-red-50 border border-red-100 p-4 rounded-xl flex items-center gap-3 animate-in shake-in duration-300">
                                <X size={20} class="text-red-600" />
                                <p class="text-sm font-bold text-red-700">{loginError}</p>
                            </div>
                        {/if}

                        <form class="space-y-4" on:submit|preventDefault={register}>
                            <div class="space-y-1">
                                <label class="text-xs font-black uppercase tracking-widest text-gray-400">Full Name</label>
                                <input 
                                    type="text" 
                                    bind:value={regName}
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
                            <button 
                                type="submit"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-black shadow-lg shadow-indigo-200 transition-all flex items-center justify-center gap-2"
                                disabled={isSubmitting}
                            >
                                {#if isSubmitting}
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Creating account...
                                {:else}
                                    Create Account
                                {/if}
                            </button>
                        </form>

                        <div class="text-center">
                            <p class="text-sm text-gray-400 font-bold">Already have an account? <button class="text-indigo-600 hover:underline" on:click={() => { showRegisterModal = false; showLoginModal = true; }}>Sign in</button></p>
                        </div>
                    </div>
                </div>
            </div>
        {/if}

        <!-- Offer Modal -->
        {#if showOfferModal}
            <div class="fixed inset-0 z-[120] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-md animate-in fade-in duration-300">
                <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden animate-in zoom-in-95 duration-300 relative">
                    <button 
                        class="absolute top-4 right-4 p-2 hover:bg-gray-100 rounded-full transition-colors"
                        on:click={() => showOfferModal = false}
                    >
                        <X size={20} class="text-gray-400" />
                    </button>

                    <div class="p-10 space-y-8">
                        <div class="text-center space-y-2">
                            <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto text-white shadow-lg shadow-indigo-200 mb-6">
                                <Plus size={32} />
                            </div>
                            <h2 class="text-3xl font-black">Make an Offer</h2>
                            <p class="text-gray-500">Propose your terms for {selectedVisitForOffer.property.name}</p>
                        </div>

                        <form class="space-y-4" on:submit|preventDefault={handleOfferSubmission}>
                            <div class="space-y-1">
                                <label class="text-xs font-black uppercase tracking-widest text-gray-400">Monthly Rent ($)</label>
                                <input 
                                    type="number" 
                                    bind:value={offerAmount}
                                    placeholder="2500"
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold text-xl text-indigo-600"
                                    required
                                />
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-black uppercase tracking-widest text-gray-400">Additional Terms</label>
                                <textarea 
                                    bind:value={offerTerms}
                                    placeholder="Any special requests or conditions..."
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-medium h-32"
                                ></textarea>
                            </div>
                            <button 
                                type="submit"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-black shadow-lg shadow-indigo-200 transition-all flex items-center justify-center gap-2"
                            >
                                Send Offer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        {/if}
    </main>
</div>

<style>
</style>