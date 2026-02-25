<x-app-layout>
    <div x-data="createSupplier()" x-init="init()">
        <!-- Breadcrumb -->
        <nav class="flex items-center text-sm text-gray-500 mb-6">
            <a href="{{ route('suppliers.index') }}" class="hover:text-gray-700 transition-colors">Suppliers</a>
            <svg class="w-4 h-4 mx-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-900 font-medium">New Supplier</span>
        </nav>

        <div class="max-w-lg">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-semibold text-gray-900">Create Supplier</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Add a new timber supplier to your system.</p>
                </div>
                <div class="card-body">
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Supplier Name</label>
                        <input type="text" id="name" x-model="form.name" @keydown.enter="submit()"
                            class="input" placeholder="Enter supplier name" autofocus>
                        <template x-if="errors.name">
                            <p class="mt-1 text-sm text-red-600" x-text="errors.name[0]"></p>
                        </template>
                    </div>
                    <div class="flex items-center justify-end space-x-3">
                        <a href="{{ route('suppliers.index') }}" class="btn btn-outline">Cancel</a>
                        <button @click="submit()" class="btn btn-primary" :disabled="submitting">
                            <template x-if="submitting">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg>
                            </template>
                            Create Supplier
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function createSupplier() {
            return {
                form: {
                    name: ''
                },
                errors: {},
                submitting: false,
                init() {},
                async submit() {
                    this.submitting = true;
                    this.errors = {};
                    try {
                        await apiRequest('/api/suppliers', {
                            method: 'POST',
                            body: JSON.stringify(this.form),
                        });
                        showToast('Supplier created successfully.');
                        window.location.href = '/suppliers';
                    } catch (e) {
                        if (e.errors) this.errors = e.errors;
                        else showToast(e.message || 'Failed to create supplier', 'error');
                    } finally {
                        this.submitting = false;
                    }
                },
            };
        }
    </script>
</x-app-layout>
