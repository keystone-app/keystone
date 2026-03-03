<script>
    import { Calendar, Clock, Upload, CheckCircle2, X } from 'lucide-svelte';
    import Modal from '../ui/Modal.svelte';
    import Button from '../ui/Button.svelte';

    let { 
        isOpen, 
        onClose, 
        property, 
        identityDoc,
        onUploadId,
        onSubmit,
        isSubmitting = false
    } = $props();

    let step = $state(1);
    let date = $state('');
    let time = $state('');
    let selectedFile = $state(null);
    let filePreviewUrl = $state('');

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

    async function handleComplete() {
        if (await onSubmit({ date, time, file: selectedFile })) {
            onClose();
            step = 1;
            removeFile();
        }
    }
</script>

<Modal {isOpen} {onClose} title="Schedule Your Visit" maxWidth="max-w-lg">
    <div class="space-y-8">
        <!-- Progress Steps -->
        <div class="flex items-center gap-4">
            <div class="flex-1 h-2 rounded-full {step >= 1 ? 'bg-indigo-600' : 'bg-gray-100'}"></div>
            <div class="flex-1 h-2 rounded-full {step >= 2 || identityDoc ? 'bg-indigo-600' : 'bg-gray-100'}"></div>
        </div>

        {#if step === 1}
            <div class="space-y-6">
                <div class="space-y-2">
                    <h3 class="text-2xl font-black">When would you like to visit?</h3>
                    <p class="text-gray-500 text-sm">Select a date and time that works for you.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="visit-date" class="text-xs font-black uppercase tracking-widest text-gray-400">Date</label>
                        <div class="relative">
                            <Calendar class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" size={18} />
                            <input id="visit-date" type="date" bind:value={date} class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold" />
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label for="visit-time" class="text-xs font-black uppercase tracking-widest text-gray-400">Time</label>
                        <div class="relative">
                            <Clock class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" size={18} />
                            <input id="visit-time" type="time" bind:value={time} class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold" />
                        </div>
                    </div>
                </div>
                <Button 
                    variant="primary" 
                    size="xl" 
                    class="w-full"
                    disabled={!date || !time}
                    onclick={() => identityDoc ? handleComplete() : step = 2}
                >
                    {identityDoc ? 'Confirm Visit' : 'Next: Verify Identity'}
                </Button>
            </div>
        {:else}
            <div class="space-y-6">
                <div class="space-y-2">
                    <h3 class="text-2xl font-black">Identity Verification</h3>
                    <p class="text-gray-500 text-sm">Please upload a valid legal document to secure your visit.</p>
                </div>
                
                <div class="relative group">
                    {#if filePreviewUrl}
                        <div class="relative rounded-3xl overflow-hidden border-2 border-indigo-600 shadow-xl">
                            <img src={filePreviewUrl} alt="Preview" class="w-full h-64 object-cover" />
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <Button variant="secondary" size="md" onclick={removeFile}>
                                    <X size={18} />
                                    Remove Image
                                </Button>
                            </div>
                        </div>
                    {:else}
                        <label for="visit-identity-file" class="border-2 border-dashed border-gray-200 rounded-3xl p-10 flex flex-col items-center justify-center text-center gap-4 bg-gray-50/50 hover:border-indigo-300 transition-colors cursor-pointer group">
                            <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center text-gray-400 group-hover:text-indigo-600 transition-colors">
                                <Upload size={32} />
                            </div>
                            <div class="space-y-1">
                                <p class="font-bold">Click to upload or drag & drop</p>
                                <p class="text-xs text-gray-400 font-medium">PNG, JPG or WEBP (max. 10MB)</p>
                            </div>
                            <input id="visit-identity-file" type="file" class="hidden" accept="image/*" onchange={handleFileSelection} />
                        </label>
                    {/if}
                </div>

                <div class="flex gap-3">
                    <Button variant="secondary" size="xl" class="flex-1" onclick={() => step = 1}>Back</Button>
                    <Button 
                        variant="primary" 
                        size="xl" 
                        class="flex-[2]"
                        onclick={handleComplete}
                        disabled={!selectedFile || isSubmitting}
                    >
                        {#if isSubmitting}
                            Processing...
                        {:else}
                            Complete Scheduling
                        {/if}
                    </Button>
                </div>
            </div>
        {/if}
    </div>
</Modal>
