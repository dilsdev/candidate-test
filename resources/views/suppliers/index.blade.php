<x-app-layout>
    <div x-data="suppliersIndex()" x-init="init()">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Suppliers</h1>
                <p class="mt-1 text-sm text-gray-500">Manage timber suppliers and material sourcing.</p>
            </div>
            <button @click="openCreateModal()" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Supplier
            </button>
        </div>

        <!-- Search & Filters -->
        <div class="flex items-center justify-between mb-4 gap-4">
            <div class="relative flex-1 max-w-sm">
                <input type="text" x-model="search" @input.debounce.300ms="filterSuppliers()"
                    class="input input-search" placeholder="Search suppliers by name...">
            </div>
            <div class="flex items-center gap-2">
                <button class="btn btn-outline btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter
                </button>
                <button @click="exportAll()" class="btn btn-outline btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <!-- Loading Skeleton -->
            <template x-if="loading">
                <div class="p-6 space-y-4">
                    <template x-for="i in 5" :key="i">
                        <div class="flex items-center space-x-4">
                            <div class="skeleton w-10 h-10 rounded-full"></div>
                            <div class="flex-1 space-y-2">
                                <div class="skeleton h-4 w-1/3"></div>
                                <div class="skeleton h-3 w-1/4"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Empty State -->
            <template x-if="!loading && filtered.length === 0 && search === ''">
                <div class="text-center py-16">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h3 class="mt-3 text-sm font-medium text-gray-900">No suppliers</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new supplier.</p>
                    <button @click="openCreateModal()" class="btn btn-primary mt-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Supplier
                    </button>
                </div>
            </template>

            <!-- No Search Results -->
            <template x-if="!loading && filtered.length === 0 && search !== ''">
                <div class="text-center py-16">
                    <p class="text-sm text-gray-500">No suppliers match "<span x-text="search"
                            class="font-medium"></span>"</p>
                </div>
            </template>

            <!-- Data Table -->
            <template x-if="!loading && filtered.length > 0">
                <div>
                    <table class="clt-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Total Layups</th>
                                <th>Created At</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="supplier in paginatedData" :key="supplier.id">
                                <tr>
                                    <td>
                                        <div class="flex items-center space-x-3">
                                            <div class="avatar-initials" :class="'avatar-color-' + (supplier.id % 8)"
                                                x-text="getInitials(supplier.name)"></div>
                                            <div>
                                                <a :href="'/suppliers/' + supplier.id"
                                                    class="font-medium text-gray-900 hover:text-green-800 transition-colors"
                                                    x-text="supplier.name"></a>
                                                <div class="text-xs text-gray-400"
                                                    x-text="'ID: SUP-' + String(supplier.id).padStart(3, '0')"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td x-text="supplier.layups_count || 0"></td>
                                    <td x-text="formatDate(supplier.created_at)"></td>
                                    <td class="text-right">
                                        <div class="flex items-center justify-end space-x-1">
                                            <a :href="'/suppliers/' + supplier.id" class="btn-icon" title="View">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <button @click="openEditModal(supplier)" class="btn-icon" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <a :href="'/suppliers/' + supplier.id + '/export'" class="btn-icon"
                                                title="Export JSON">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </a>
                                            <button @click="confirmDelete(supplier)"
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

                    <!-- Pagination -->
                    <div class="pagination">
                        <span class="pagination-info">
                            Showing <span x-text="paginationStart"></span> to <span x-text="paginationEnd"></span> of
                            <span x-text="filtered.length"></span> results
                        </span>
                        <div class="pagination-buttons">
                            <button @click="prevPage()" :disabled="page === 1"
                                class="pagination-btn">&lsaquo;</button>
                            <button @click="nextPage()" :disabled="page >= totalPages"
                                class="pagination-btn">&rsaquo;</button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Create/Edit Modal -->
        <template x-if="showModal">
            <div class="modal-overlay" @click.self="showModal = false">
                <div class="modal-content" @click.stop>
                    <div class="modal-header">
                        <h3 class="text-lg font-semibold text-gray-900"
                            x-text="editingSupplier ? 'Edit Supplier' : 'Add Supplier'"></h3>
                        <button @click="showModal = false" class="btn-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Name</label>
                            <input type="text" x-model="form.name" @keydown.enter="submitForm()" class="input"
                                placeholder="Enter supplier name" autofocus>
                            <template x-if="formErrors.name">
                                <p class="mt-1 text-sm text-red-600" x-text="formErrors.name[0]"></p>
                            </template>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button @click="showModal = false" class="btn btn-outline">Cancel</button>
                        <button @click="submitForm()" class="btn btn-primary" :disabled="submitting">
                            <template x-if="submitting">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg>
                            </template>
                            <span x-text="editingSupplier ? 'Update Supplier' : 'Create Supplier'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <!-- Delete Confirmation Modal -->
        <template x-if="showDeleteModal">
            <div class="modal-overlay" @click.self="showDeleteModal = false">
                <div class="modal-content" @click.stop>
                    <div class="modal-header">
                        <h3 class="text-lg font-semibold text-gray-900">Delete Supplier</h3>
                        <button @click="showDeleteModal = false" class="btn-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="text-sm text-gray-600">Are you sure you want to delete <strong
                                x-text="deletingSupplier?.name"></strong>? All associated layups and layers will be
                            permanently deleted.</p>
                    </div>
                    <div class="modal-footer">
                        <button @click="showDeleteModal = false" class="btn btn-outline">Cancel</button>
                        <button @click="deleteSupplier()" class="btn btn-danger" :disabled="submitting">
                            <template x-if="submitting">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg>
                            </template>
                            Delete Supplier
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script>
        function suppliersIndex() {
            return {
                suppliers: [],
                filtered: [],
                loading: true,
                search: '',
                page: 1,
                perPage: 5,
                showModal: false,
                showDeleteModal: false,
                editingSupplier: null,
                deletingSupplier: null,
                submitting: false,
                form: {
                    name: ''
                },
                formErrors: {},

                async init() {
                    await this.fetchSuppliers();
                },

                async fetchSuppliers() {
                    this.loading = true;
                    try {
                        const data = await apiRequest('/api/suppliers');
                        this.suppliers = data.data;
                        this.filterSuppliers();
                    } catch (e) {
                        showToast('Failed to load suppliers', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                filterSuppliers() {
                    const q = this.search.toLowerCase().trim();
                    this.filtered = q ?
                        this.suppliers.filter(s => s.name.toLowerCase().includes(q)) :
                        [...this.suppliers];
                    this.page = 1;
                },

                get paginatedData() {
                    const start = (this.page - 1) * this.perPage;
                    return this.filtered.slice(start, start + this.perPage);
                },

                get totalPages() {
                    return Math.ceil(this.filtered.length / this.perPage);
                },

                get paginationStart() {
                    return this.filtered.length === 0 ? 0 : (this.page - 1) * this.perPage + 1;
                },

                get paginationEnd() {
                    return Math.min(this.page * this.perPage, this.filtered.length);
                },

                prevPage() {
                    if (this.page > 1) this.page--;
                },
                nextPage() {
                    if (this.page < this.totalPages) this.page++;
                },

                getInitials(name) {
                    return name.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();
                },

                formatDate(dateStr) {
                    if (!dateStr) return '-';
                    const d = new Date(dateStr);
                    return d.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: '2-digit'
                    });
                },

                openCreateModal() {
                    this.editingSupplier = null;
                    this.form = {
                        name: ''
                    };
                    this.formErrors = {};
                    this.showModal = true;
                },

                openEditModal(supplier) {
                    this.editingSupplier = supplier;
                    this.form = {
                        name: supplier.name
                    };
                    this.formErrors = {};
                    this.showModal = true;
                },

                async submitForm() {
                    this.submitting = true;
                    this.formErrors = {};
                    try {
                        if (this.editingSupplier) {
                            await apiRequest(`/api/suppliers/${this.editingSupplier.id}`, {
                                method: 'PUT',
                                body: JSON.stringify(this.form),
                            });
                            showToast('Supplier updated successfully.');
                        } else {
                            await apiRequest('/api/suppliers', {
                                method: 'POST',
                                body: JSON.stringify(this.form),
                            });
                            showToast('Supplier created successfully.');
                        }
                        this.showModal = false;
                        await this.fetchSuppliers();
                    } catch (e) {
                        if (e.errors) {
                            this.formErrors = e.errors;
                        } else {
                            showToast(e.message || 'An error occurred', 'error');
                        }
                    } finally {
                        this.submitting = false;
                    }
                },

                confirmDelete(supplier) {
                    this.deletingSupplier = supplier;
                    this.showDeleteModal = true;
                },

                async deleteSupplier() {
                    this.submitting = true;
                    try {
                        await apiRequest(`/api/suppliers/${this.deletingSupplier.id}`, {
                            method: 'DELETE'
                        });
                        showToast('Supplier deleted successfully.');
                        this.showDeleteModal = false;
                        await this.fetchSuppliers();
                    } catch (e) {
                        showToast(e.message || 'Failed to delete supplier', 'error');
                    } finally {
                        this.submitting = false;
                    }
                },

                exportAll() {
                    // Export all suppliers - generic action
                    showToast('Export feature coming soon', 'error');
                },
            };
        }
    </script>
</x-app-layout>
