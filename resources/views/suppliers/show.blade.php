<x-app-layout>
    <div x-data="supplierShow({{ $supplier->id }}, {{ Js::from($supplier->loadMissing('layups.layers')->toArray()) }})" x-init="init()">
        <!-- Breadcrumb -->
        <nav class="flex items-center text-sm text-gray-500 mb-6">
            <a href="{{ route('suppliers.index') }}" class="hover:text-gray-700 transition-colors">Suppliers</a>
            <svg class="w-4 h-4 mx-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-900 font-medium"
                x-text="supplier?.name || '{{ $supplier->name }}'">{{ $supplier->name }}</span>
        </nav>

        <!-- Supplier Header Card -->
        <div class="card mb-6">
            <div class="card-body">
                <div class="flex items-start justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="avatar-initials lg" :class="'avatar-color-' + ({{ $supplier->id }} % 8)"
                            x-text="getInitials(supplier?.name || '{{ $supplier->name }}')">
                        </div>
                        <div>
                            <div class="flex items-center space-x-3">
                                <h1 class="text-xl font-bold text-gray-900"
                                    x-text="supplier?.name || '{{ $supplier->name }}'">{{ $supplier->name }}</h1>
                                <span class="badge badge-active">Active</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-0.5"
                                x-text="'ID: SUP-' + String({{ $supplier->id }}).padStart(3, '0')">ID:
                                SUP-{{ str_pad($supplier->id, 3, '0', STR_PAD_LEFT) }}</p>
                        </div>
                    </div>
                    <button @click="openEditModal()" class="btn btn-outline btn-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Supplier
                    </button>
                </div>

                <!-- Info Grid -->
                <div class="info-grid mt-6">
                    <div class="info-card">
                        <div class="info-card-label">Total Layups</div>
                        <div class="info-card-value">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span x-text="layups.length || 0">{{ $supplier->layups->count() }}</span>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-card-label">Total Layers</div>
                        <div class="info-card-value">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            <span x-text="totalLayers">0</span>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-card-label">Created</div>
                        <div class="info-card-value">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span
                                x-text="formatDate(supplier?.created_at)">{{ $supplier->created_at?->format('M d, Y') }}</span>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-card-label">Last Updated</div>
                        <div class="info-card-value">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span
                                x-text="formatDate(supplier?.updated_at)">{{ $supplier->updated_at?->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Layups Section -->
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Associated Layups</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Manage CLT layup configurations for this supplier.</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('suppliers.import.form', $supplier) }}" class="btn btn-outline btn-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Import
                    </a>
                    <a href="{{ route('suppliers.export', $supplier) }}" class="btn btn-outline btn-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export
                    </a>
                    <button @click="openCreateLayupModal()" class="btn btn-primary btn-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Add Layup
                    </button>
                </div>
            </div>

            <!-- Loading -->
            <template x-if="loading">
                <div class="p-6 space-y-4">
                    <template x-for="i in 3" :key="i">
                        <div class="flex items-center space-x-4">
                            <div class="skeleton h-4 w-16"></div>
                            <div class="skeleton h-4 w-32"></div>
                            <div class="skeleton h-4 w-12"></div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Empty -->
            <template x-if="!loading && layups.length === 0">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    <h3 class="mt-3 text-sm font-medium text-gray-900">No layups yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Create your first layup configuration.</p>
                    <button @click="openCreateLayupModal()" class="btn btn-primary btn-sm mt-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Add Layup
                    </button>
                </div>
            </template>

            <!-- Layups Table -->
            <template x-if="!loading && layups.length > 0">
                <div>
                    <table class="clt-table">
                        <thead>
                            <tr>
                                <th>Layup ID</th>
                                <th>Name</th>
                                <th>Ply Count</th>
                                <th>Created</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="layup in layups" :key="layup.id">
                                <tr>
                                    <td>
                                        <span class="text-xs font-mono text-gray-500"
                                            x-text="'LYP-' + String(layup.id).padStart(3, '0')"></span>
                                    </td>
                                    <td>
                                        <a :href="'/suppliers/' + {{ $supplier->id }} + '/layups/' + layup.id"
                                            class="font-medium text-gray-900 hover:text-green-800 transition-colors"
                                            x-text="layup.name"></a>
                                    </td>
                                    <td>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700"
                                            x-text="(layup.layers_count || 0) + ' layers'"></span>
                                    </td>
                                    <td x-text="formatDate(layup.created_at)"></td>
                                    <td class="text-right">
                                        <div class="flex items-center justify-end space-x-1">
                                            <a :href="'/suppliers/' + {{ $supplier->id }} + '/layups/' + layup.id"
                                                class="btn-icon" title="View Layers">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <button @click="openEditLayupModal(layup)" class="btn-icon"
                                                title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button @click="confirmDeleteLayup(layup)"
                                                class="btn-icon hover:!text-red-600" title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </template>
        </div>

        <!-- Edit Supplier Modal -->
        <template x-if="showEditModal">
            <div class="modal-overlay" @click.self="showEditModal = false">
                <div class="modal-content" @click.stop>
                    <div class="modal-header">
                        <h3 class="text-lg font-semibold text-gray-900">Edit Supplier</h3>
                        <button @click="showEditModal = false" class="btn-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Name</label>
                            <input type="text" x-model="editForm.name" @keydown.enter="updateSupplier()"
                                class="input" autofocus>
                            <template x-if="editErrors.name">
                                <p class="mt-1 text-sm text-red-600" x-text="editErrors.name[0]"></p>
                            </template>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button @click="showEditModal = false" class="btn btn-outline">Cancel</button>
                        <button @click="updateSupplier()" class="btn btn-primary" :disabled="submitting">
                            <template x-if="submitting">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg>
                            </template>
                            Update Supplier
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <!-- Create/Edit Layup Modal -->
        <template x-if="showLayupModal">
            <div class="modal-overlay" @click.self="showLayupModal = false">
                <div class="modal-content" @click.stop>
                    <div class="modal-header">
                        <h3 class="text-lg font-semibold text-gray-900"
                            x-text="editingLayup ? 'Edit Layup' : 'Add Layup'"></h3>
                        <button @click="showLayupModal = false" class="btn-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Layup Name</label>
                            <input type="text" x-model="layupForm.name" @keydown.enter="submitLayup()"
                                class="input" placeholder="Enter layup name" autofocus>
                            <template x-if="layupErrors.name">
                                <p class="mt-1 text-sm text-red-600" x-text="layupErrors.name[0]"></p>
                            </template>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button @click="showLayupModal = false" class="btn btn-outline">Cancel</button>
                        <button @click="submitLayup()" class="btn btn-primary" :disabled="submitting">
                            <template x-if="submitting">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg>
                            </template>
                            <span x-text="editingLayup ? 'Update Layup' : 'Create Layup'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <!-- Delete Layup Modal -->
        <template x-if="showDeleteLayupModal">
            <div class="modal-overlay" @click.self="showDeleteLayupModal = false">
                <div class="modal-content" @click.stop>
                    <div class="modal-header">
                        <h3 class="text-lg font-semibold text-gray-900">Delete Layup</h3>
                        <button @click="showDeleteLayupModal = false" class="btn-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="text-sm text-gray-600">Are you sure you want to delete <strong
                                x-text="deletingLayup?.name"></strong>? All associated layers will be permanently
                            deleted.</p>
                    </div>
                    <div class="modal-footer">
                        <button @click="showDeleteLayupModal = false" class="btn btn-outline">Cancel</button>
                        <button @click="deleteLayup()" class="btn btn-danger" :disabled="submitting">
                            <template x-if="submitting">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg>
                            </template>
                            Delete Layup
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script>
        function supplierShow(supplierId, serverData) {
            return {
                supplierId: supplierId,
                supplier: serverData || null,
                layups: serverData?.layups || [],
                loading: false,
                submitting: false,

                // Edit Supplier
                showEditModal: false,
                editForm: {
                    name: ''
                },
                editErrors: {},

                // Layup CRUD
                showLayupModal: false,
                showDeleteLayupModal: false,
                editingLayup: null,
                deletingLayup: null,
                layupForm: {
                    name: ''
                },
                layupErrors: {},

                async init() {
                    // Data already seeded from server, no initial fetch needed
                },

                async fetchSupplier() {
                    this.loading = true;
                    try {
                        const data = await apiRequest(`/api/suppliers/${this.supplierId}`);
                        this.supplier = data.data;
                        this.layups = data.data.layups || [];
                    } catch (e) {
                        showToast('Failed to load supplier details', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                get totalLayers() {
                    return this.layups.reduce((sum, l) => sum + (l.layers_count || 0), 0);
                },

                getInitials(name) {
                    if (!name) return '?';
                    return name.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();
                },

                formatDate(dateStr) {
                    if (!dateStr) return '-';
                    return new Date(dateStr).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: '2-digit'
                    });
                },

                // Edit Supplier
                openEditModal() {
                    this.editForm = {
                        name: this.supplier?.name || ''
                    };
                    this.editErrors = {};
                    this.showEditModal = true;
                },

                async updateSupplier() {
                    this.submitting = true;
                    this.editErrors = {};
                    try {
                        await apiRequest(`/api/suppliers/${this.supplierId}`, {
                            method: 'PUT',
                            body: JSON.stringify(this.editForm),
                        });
                        showToast('Supplier updated successfully.');
                        this.showEditModal = false;
                        await this.fetchSupplier();
                    } catch (e) {
                        if (e.errors) this.editErrors = e.errors;
                        else showToast(e.message || 'Failed to update', 'error');
                    } finally {
                        this.submitting = false;
                    }
                },

                // Layup CRUD
                openCreateLayupModal() {
                    this.editingLayup = null;
                    this.layupForm = {
                        name: ''
                    };
                    this.layupErrors = {};
                    this.showLayupModal = true;
                },

                openEditLayupModal(layup) {
                    this.editingLayup = layup;
                    this.layupForm = {
                        name: layup.name
                    };
                    this.layupErrors = {};
                    this.showLayupModal = true;
                },

                async submitLayup() {
                    this.submitting = true;
                    this.layupErrors = {};
                    try {
                        if (this.editingLayup) {
                            await apiRequest(`/api/suppliers/${this.supplierId}/layups/${this.editingLayup.id}`, {
                                method: 'PUT',
                                body: JSON.stringify(this.layupForm),
                            });
                            showToast('Layup updated successfully.');
                        } else {
                            await apiRequest(`/api/suppliers/${this.supplierId}/layups`, {
                                method: 'POST',
                                body: JSON.stringify(this.layupForm),
                            });
                            showToast('Layup created successfully.');
                        }
                        this.showLayupModal = false;
                        await this.fetchSupplier();
                    } catch (e) {
                        if (e.errors) this.layupErrors = e.errors;
                        else showToast(e.message || 'An error occurred', 'error');
                    } finally {
                        this.submitting = false;
                    }
                },

                confirmDeleteLayup(layup) {
                    this.deletingLayup = layup;
                    this.showDeleteLayupModal = true;
                },

                async deleteLayup() {
                    this.submitting = true;
                    try {
                        await apiRequest(`/api/suppliers/${this.supplierId}/layups/${this.deletingLayup.id}`, {
                            method: 'DELETE'
                        });
                        showToast('Layup deleted successfully.');
                        this.showDeleteLayupModal = false;
                        await this.fetchSupplier();
                    } catch (e) {
                        showToast(e.message || 'Failed to delete layup', 'error');
                    } finally {
                        this.submitting = false;
                    }
                },
            };
        }
    </script>
</x-app-layout>
