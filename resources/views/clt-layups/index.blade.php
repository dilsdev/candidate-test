<x-app-layout>
    <div x-data="layupsIndex({{ $supplier->id }})" x-init="init()">
        <!-- Breadcrumb -->
        <nav class="flex items-center text-sm text-gray-500 mb-6">
            <a href="{{ route('suppliers.index') }}" class="hover:text-gray-700 transition-colors">Suppliers</a>
            <svg class="w-4 h-4 mx-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <a href="{{ route('suppliers.show', $supplier) }}"
                class="hover:text-gray-700 transition-colors">{{ $supplier->name }}</a>
            <svg class="w-4 h-4 mx-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-900 font-medium">Layups</span>
        </nav>

        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">CLT Layups</h1>
                <p class="mt-1 text-sm text-gray-500">Layup configurations for <span
                        class="font-medium">{{ $supplier->name }}</span></p>
            </div>
            <button @click="openCreateModal()" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Layup
            </button>
        </div>

        <!-- Search -->
        <div class="relative max-w-sm mb-4">
            <input type="text" x-model="search" @input.debounce.300ms="filterLayups()" class="input input-search"
                placeholder="Search layups...">
        </div>

        <!-- Table -->
        <div class="card">
            <template x-if="loading">
                <div class="p-6 space-y-4">
                    <template x-for="i in 3" :key="i">
                        <div class="skeleton h-4 w-full"></div>
                    </template>
                </div>
            </template>

            <template x-if="!loading && filtered.length === 0">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    <h3 class="mt-3 text-sm font-medium text-gray-900">No layups found</h3>
                    <p class="mt-1 text-sm text-gray-500"
                        x-text="search ? 'No matching layups.' : 'Create your first layup configuration.'"></p>
                </div>
            </template>

            <template x-if="!loading && filtered.length > 0">
                <div>
                    <table class="clt-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Layers</th>
                                <th>Created</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="layup in filtered" :key="layup.id">
                                <tr>
                                    <td><span class="text-xs font-mono text-gray-500"
                                            x-text="'LYP-' + String(layup.id).padStart(3, '0')"></span></td>
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
                                                class="btn-icon" title="View">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <button @click="openEditModal(layup)" class="btn-icon" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button @click="confirmDelete(layup)" class="btn-icon hover:!text-red-600"
                                                title="Delete">
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

        <!-- Create/Edit Modal -->
        <template x-if="showModal">
            <div class="modal-overlay" @click.self="showModal = false">
                <div class="modal-content" @click.stop>
                    <div class="modal-header">
                        <h3 class="text-lg font-semibold text-gray-900" x-text="editing ? 'Edit Layup' : 'Add Layup'">
                        </h3>
                        <button @click="showModal = false" class="btn-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Layup Name</label>
                        <input type="text" x-model="form.name" @keydown.enter="submitForm()" class="input"
                            placeholder="Enter layup name" autofocus>
                        <template x-if="formErrors.name">
                            <p class="mt-1 text-sm text-red-600" x-text="formErrors.name[0]"></p>
                        </template>
                    </div>
                    <div class="modal-footer">
                        <button @click="showModal = false" class="btn btn-outline">Cancel</button>
                        <button @click="submitForm()" class="btn btn-primary" :disabled="submitting">
                            <span x-text="editing ? 'Update' : 'Create'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <!-- Delete Modal -->
        <template x-if="showDeleteModal">
            <div class="modal-overlay" @click.self="showDeleteModal = false">
                <div class="modal-content" @click.stop>
                    <div class="modal-header">
                        <h3 class="text-lg font-semibold text-gray-900">Delete Layup</h3>
                        <button @click="showDeleteModal = false" class="btn-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="text-sm text-gray-600">Are you sure you want to delete <strong
                                x-text="deleting?.name"></strong>? All layers will be permanently removed.</p>
                    </div>
                    <div class="modal-footer">
                        <button @click="showDeleteModal = false" class="btn btn-outline">Cancel</button>
                        <button @click="performDelete()" class="btn btn-danger" :disabled="submitting">Delete</button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script>
        function layupsIndex(supplierId) {
            return {
                supplierId,
                layups: [],
                filtered: [],
                loading: true,
                search: '',
                showModal: false,
                showDeleteModal: false,
                editing: null,
                deleting: null,
                submitting: false,
                form: {
                    name: ''
                },
                formErrors: {},

                async init() {
                    await this.fetch();
                },

                async fetch() {
                    this.loading = true;
                    try {
                        const data = await apiRequest(`/api/suppliers/${this.supplierId}/layups`);
                        this.layups = data.data;
                        this.filterLayups();
                    } catch (e) {
                        showToast('Failed to load layups', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                filterLayups() {
                    const q = this.search.toLowerCase().trim();
                    this.filtered = q ? this.layups.filter(l => l.name.toLowerCase().includes(q)) : [...this.layups];
                },

                formatDate(d) {
                    if (!d) return '-';
                    return new Date(d).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: '2-digit'
                    });
                },

                openCreateModal() {
                    this.editing = null;
                    this.form = {
                        name: ''
                    };
                    this.formErrors = {};
                    this.showModal = true;
                },

                openEditModal(layup) {
                    this.editing = layup;
                    this.form = {
                        name: layup.name
                    };
                    this.formErrors = {};
                    this.showModal = true;
                },

                async submitForm() {
                    this.submitting = true;
                    this.formErrors = {};
                    try {
                        if (this.editing) {
                            await apiRequest(`/api/suppliers/${this.supplierId}/layups/${this.editing.id}`, {
                                method: 'PUT',
                                body: JSON.stringify(this.form)
                            });
                            showToast('Layup updated.');
                        } else {
                            await apiRequest(`/api/suppliers/${this.supplierId}/layups`, {
                                method: 'POST',
                                body: JSON.stringify(this.form)
                            });
                            showToast('Layup created.');
                        }
                        this.showModal = false;
                        await this.fetch();
                    } catch (e) {
                        if (e.errors) this.formErrors = e.errors;
                        else showToast(e.message || 'Error', 'error');
                    } finally {
                        this.submitting = false;
                    }
                },

                confirmDelete(layup) {
                    this.deleting = layup;
                    this.showDeleteModal = true;
                },

                async performDelete() {
                    this.submitting = true;
                    try {
                        await apiRequest(`/api/suppliers/${this.supplierId}/layups/${this.deleting.id}`, {
                            method: 'DELETE'
                        });
                        showToast('Layup deleted.');
                        this.showDeleteModal = false;
                        await this.fetch();
                    } catch (e) {
                        showToast(e.message || 'Failed to delete', 'error');
                    } finally {
                        this.submitting = false;
                    }
                },
            };
        }
    </script>
</x-app-layout>
