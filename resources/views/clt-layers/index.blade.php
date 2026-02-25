<x-app-layout>
    <div x-data="layersIndex({{ $supplier->id }}, {{ $layup->id }})" x-init="init()">
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
            <a href="{{ route('suppliers.layups.show', [$supplier, $layup]) }}"
                class="hover:text-gray-700 transition-colors">{{ $layup->name }}</a>
            <svg class="w-4 h-4 mx-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-900 font-medium">Layers</span>
        </nav>

        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">CLT Layers</h1>
                <p class="mt-1 text-sm text-gray-500">Layers for layup <span
                        class="font-medium">{{ $layup->name }}</span></p>
            </div>
            <button @click="openCreateModal()" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Layer
            </button>
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

            <template x-if="!loading && layers.length === 0">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    <h3 class="mt-3 text-sm font-medium text-gray-900">No layers yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Add your first layer to this layup.</p>
                    <button @click="openCreateModal()" class="btn btn-primary btn-sm mt-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Layer
                    </button>
                </div>
            </template>

            <template x-if="!loading && layers.length > 0">
                <div>
                    <table class="clt-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Thickness (mm)</th>
                                <th>Width (mm)</th>
                                <th>Angle (°)</th>
                                <th>Direction</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="layer in sortedLayers" :key="layer.id">
                                <tr>
                                    <td>
                                        <span
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold text-white"
                                            :style="'background-color:' + (layer.angle == 0 ? '#C4956A' : '#8B6F47')"
                                            x-text="layer.layer_order"></span>
                                    </td>
                                    <td x-text="Number(layer.thickness).toFixed(2)"></td>
                                    <td x-text="Number(layer.width).toFixed(2)"></td>
                                    <td><span x-text="layer.angle + '°'"></span></td>
                                    <td>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                            :class="layer.angle == 0 ? 'bg-amber-100 text-amber-800' :
                                                'bg-yellow-100 text-yellow-800'"
                                            x-text="layer.angle == 0 ? 'Longitudinal' : 'Transverse'"></span>
                                    </td>
                                    <td class="text-right">
                                        <div class="flex items-center justify-end space-x-1">
                                            <button @click="openEditModal(layer)" class="btn-icon" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button @click="confirmDelete(layer)" class="btn-icon hover:!text-red-600"
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

                    <!-- Summary -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Total: <span class="font-medium text-gray-900"
                                    x-text="layers.length + ' layers'"></span></span>
                            <span class="text-gray-500">Total Thickness: <span class="font-medium text-gray-900"
                                    x-text="totalThickness.toFixed(2) + ' mm'"></span></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Create/Edit Modal -->
        <template x-if="showModal">
            <div class="modal-overlay" @click.self="showModal = false">
                <div class="modal-content" @click.stop style="max-width: 36rem;">
                    <div class="modal-header">
                        <h3 class="text-lg font-semibold text-gray-900" x-text="editing ? 'Edit Layer' : 'Add Layer'">
                        </h3>
                        <button @click="showModal = false" class="btn-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Layer Order</label>
                                <input type="number" x-model.number="form.layer_order" class="input"
                                    min="1" placeholder="e.g. 1">
                                <template x-if="formErrors.layer_order">
                                    <p class="mt-1 text-sm text-red-600" x-text="formErrors.layer_order[0]"></p>
                                </template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Angle (°)</label>
                                <input type="number" x-model.number="form.angle" class="input" step="0.01"
                                    placeholder="e.g. 0 or 90">
                                <template x-if="formErrors.angle">
                                    <p class="mt-1 text-sm text-red-600" x-text="formErrors.angle[0]"></p>
                                </template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Thickness (mm)</label>
                                <input type="number" x-model.number="form.thickness" class="input" step="0.01"
                                    min="0" placeholder="e.g. 20.00">
                                <template x-if="formErrors.thickness">
                                    <p class="mt-1 text-sm text-red-600" x-text="formErrors.thickness[0]"></p>
                                </template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Width (mm)</label>
                                <input type="number" x-model.number="form.width" class="input" step="0.01"
                                    min="0" placeholder="e.g. 100.00">
                                <template x-if="formErrors.width">
                                    <p class="mt-1 text-sm text-red-600" x-text="formErrors.width[0]"></p>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button @click="showModal = false" class="btn btn-outline">Cancel</button>
                        <button @click="submitForm()" class="btn btn-primary" :disabled="submitting">
                            <span x-text="editing ? 'Update Layer' : 'Create Layer'"></span>
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
                        <h3 class="text-lg font-semibold text-gray-900">Delete Layer</h3>
                        <button @click="showDeleteModal = false" class="btn-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="text-sm text-gray-600">Are you sure you want to delete Layer #<span
                                x-text="deleting?.layer_order"></span>?</p>
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
        function layersIndex(supplierId, layupId) {
            return {
                supplierId,
                layupId,
                layers: [],
                loading: true,
                showModal: false,
                showDeleteModal: false,
                editing: null,
                deleting: null,
                submitting: false,
                form: {
                    layer_order: '',
                    thickness: '',
                    width: '',
                    angle: ''
                },
                formErrors: {},

                async init() {
                    await this.fetch();
                },

                async fetch() {
                    this.loading = true;
                    try {
                        const data = await apiRequest(
                        `/api/suppliers/${this.supplierId}/layups/${this.layupId}/layers`);
                        this.layers = data.data;
                    } catch (e) {
                        showToast('Failed to load layers', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                get sortedLayers() {
                    return [...this.layers].sort((a, b) => a.layer_order - b.layer_order);
                },

                get totalThickness() {
                    return this.layers.reduce((s, l) => s + (parseFloat(l.thickness) || 0), 0);
                },

                openCreateModal() {
                    this.editing = null;
                    const next = this.layers.length > 0 ? Math.max(...this.layers.map(l => l.layer_order)) + 1 : 1;
                    this.form = {
                        layer_order: next,
                        thickness: '',
                        width: '',
                        angle: ''
                    };
                    this.formErrors = {};
                    this.showModal = true;
                },

                openEditModal(layer) {
                    this.editing = layer;
                    this.form = {
                        layer_order: layer.layer_order,
                        thickness: layer.thickness,
                        width: layer.width,
                        angle: layer.angle
                    };
                    this.formErrors = {};
                    this.showModal = true;
                },

                async submitForm() {
                    this.submitting = true;
                    this.formErrors = {};
                    try {
                        const url = this.editing ?
                            `/api/suppliers/${this.supplierId}/layups/${this.layupId}/layers/${this.editing.id}` :
                            `/api/suppliers/${this.supplierId}/layups/${this.layupId}/layers`;
                        await apiRequest(url, {
                            method: this.editing ? 'PUT' : 'POST',
                            body: JSON.stringify(this.form),
                        });
                        showToast(this.editing ? 'Layer updated.' : 'Layer created.');
                        this.showModal = false;
                        await this.fetch();
                    } catch (e) {
                        if (e.errors) this.formErrors = e.errors;
                        else showToast(e.message || 'Error', 'error');
                    } finally {
                        this.submitting = false;
                    }
                },

                confirmDelete(layer) {
                    this.deleting = layer;
                    this.showDeleteModal = true;
                },

                async performDelete() {
                    this.submitting = true;
                    try {
                        await apiRequest(
                            `/api/suppliers/${this.supplierId}/layups/${this.layupId}/layers/${this.deleting.id}`, {
                                method: 'DELETE'
                            });
                        showToast('Layer deleted.');
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
