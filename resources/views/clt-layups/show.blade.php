<x-app-layout>
    <div x-data="layupShow({{ $supplier->id }}, {{ $layup->id }})" x-init="init()">
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
            <span class="text-gray-900 font-medium"
                x-text="layup?.name || '{{ $layup->name }}'">{{ $layup->name }}</span>
        </nav>

        <!-- Layup Header -->
        <div class="card mb-6">
            <div class="card-body">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center space-x-3">
                            <h1 class="text-xl font-bold text-gray-900" x-text="layup?.name || '{{ $layup->name }}'">
                                {{ $layup->name }}</h1>
                            <span class="badge badge-active">Active</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1"
                            x-text="'ID: LYP-' + String({{ $layup->id }}).padStart(3, '0')">
                            ID: LYP-{{ str_pad($layup->id, 3, '0', STR_PAD_LEFT) }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button @click="openEditLayupModal()" class="btn btn-outline btn-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Layup
                        </button>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="info-grid mt-6">
                    <div class="info-card">
                        <div class="info-card-label">Total Layers</div>
                        <div class="info-card-value">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            <span x-text="layers.length">0</span>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-card-label">Total Thickness</div>
                        <div class="info-card-value">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span x-text="totalThickness.toFixed(2) + ' mm'">0 mm</span>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-card-label">Created</div>
                        <div class="info-card-value">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span x-text="formatDate(layup?.created_at)">-</span>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-card-label">Last Modified</div>
                        <div class="info-card-value">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-text="formatDate(layup?.updated_at)">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Layer Composition + Structure Visualizer -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Layer Composition Table -->
            <div class="lg:col-span-2 card">
                <div class="card-header flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Layer Composition</h2>
                        <p class="text-sm text-gray-500 mt-0.5">Individual layers in this CLT layup</p>
                    </div>
                    <button @click="openCreateLayerModal()" class="btn btn-primary btn-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Layer
                    </button>
                </div>

                <template x-if="loading">
                    <div class="p-6 space-y-3">
                        <template x-for="i in 3" :key="i">
                            <div class="skeleton h-4 w-full"></div>
                        </template>
                    </div>
                </template>

                <template x-if="!loading && layers.length === 0">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        <p class="mt-3 text-sm text-gray-500">No layers yet. Add your first layer.</p>
                    </div>
                </template>

                <template x-if="!loading && layers.length > 0">
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
                                            <button @click="openEditLayerModal(layer)" class="btn-icon"
                                                title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button @click="confirmDeleteLayer(layer)"
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
                </template>
            </div>

            <!-- Structure Visualizer -->
            <div class="card">
                <div class="card-header">
                    <h2 class="text-base font-semibold text-gray-900">Structure Visualizer</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Cross-section view of layer composition</p>
                </div>
                <div class="card-body">
                    <template x-if="layers.length === 0">
                        <div class="text-center py-8">
                            <p class="text-xs text-gray-400">Add layers to see the visualization</p>
                        </div>
                    </template>
                    <template x-if="layers.length > 0">
                        <div class="layer-viz">
                            <template x-for="layer in sortedLayers" :key="layer.id">
                                <div class="layer-viz-item"
                                    :class="layer.angle == 0 ? 'layer-viz-longitudinal' : 'layer-viz-transverse'"
                                    :style="'min-height:' + Math.max(24, layer.thickness * 1.5) + 'px'">
                                    <span class="text-xs" x-text="'Layer ' + layer.layer_order"></span>
                                    <span class="text-xs opacity-75"
                                        x-text="layer.thickness + 'mm · ' + layer.angle + '°'"></span>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Legend -->
                    <div class="mt-4 flex items-center justify-center space-x-4">
                        <div class="flex items-center space-x-1.5">
                            <div class="w-3 h-3 rounded" style="background-color: #C4956A;"></div>
                            <span class="text-xs text-gray-500">Longitudinal (0°)</span>
                        </div>
                        <div class="flex items-center space-x-1.5">
                            <div class="w-3 h-3 rounded" style="background-color: #8B6F47;"></div>
                            <span class="text-xs text-gray-500">Transverse (90°)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Engineering Note -->
        <div class="engineering-note">
            <div class="flex items-start space-x-2">
                <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-sm font-medium text-blue-800">Engineering Note</p>
                    <p class="text-sm text-blue-700 mt-0.5">
                        CLT panels are composed of alternating layers of lumber boards. Layers at 0° are oriented along
                        the major axis (longitudinal),
                        while layers at 90° are perpendicular (transverse). The alternating pattern provides structural
                        stability and dimensional stability.
                    </p>
                </div>
            </div>
        </div>

        <!-- Edit Layup Modal -->
        <template x-if="showEditLayupModal">
            <div class="modal-overlay" @click.self="showEditLayupModal = false">
                <div class="modal-content" @click.stop>
                    <div class="modal-header">
                        <h3 class="text-lg font-semibold text-gray-900">Edit Layup</h3>
                        <button @click="showEditLayupModal = false" class="btn-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Layup Name</label>
                        <input type="text" x-model="layupForm.name" @keydown.enter="updateLayup()" class="input"
                            autofocus>
                        <template x-if="layupErrors.name">
                            <p class="mt-1 text-sm text-red-600" x-text="layupErrors.name[0]"></p>
                        </template>
                    </div>
                    <div class="modal-footer">
                        <button @click="showEditLayupModal = false" class="btn btn-outline">Cancel</button>
                        <button @click="updateLayup()" class="btn btn-primary" :disabled="submitting">Update</button>
                    </div>
                </div>
            </div>
        </template>

        <!-- Create/Edit Layer Modal -->
        <template x-if="showLayerModal">
            <div class="modal-overlay" @click.self="showLayerModal = false">
                <div class="modal-content" @click.stop style="max-width: 36rem;">
                    <div class="modal-header">
                        <h3 class="text-lg font-semibold text-gray-900"
                            x-text="editingLayer ? 'Edit Layer' : 'Add Layer'"></h3>
                        <button @click="showLayerModal = false" class="btn-icon">
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
                                <input type="number" x-model.number="layerForm.layer_order" class="input"
                                    min="1" placeholder="e.g. 1">
                                <template x-if="layerErrors.layer_order">
                                    <p class="mt-1 text-sm text-red-600" x-text="layerErrors.layer_order[0]"></p>
                                </template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Angle (°)</label>
                                <input type="number" x-model.number="layerForm.angle" class="input" step="0.01"
                                    placeholder="e.g. 0 or 90">
                                <template x-if="layerErrors.angle">
                                    <p class="mt-1 text-sm text-red-600" x-text="layerErrors.angle[0]"></p>
                                </template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Thickness (mm)</label>
                                <input type="number" x-model.number="layerForm.thickness" class="input"
                                    step="0.01" min="0" placeholder="e.g. 20.00">
                                <template x-if="layerErrors.thickness">
                                    <p class="mt-1 text-sm text-red-600" x-text="layerErrors.thickness[0]"></p>
                                </template>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Width (mm)</label>
                                <input type="number" x-model.number="layerForm.width" class="input" step="0.01"
                                    min="0" placeholder="e.g. 100.00">
                                <template x-if="layerErrors.width">
                                    <p class="mt-1 text-sm text-red-600" x-text="layerErrors.width[0]"></p>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button @click="showLayerModal = false" class="btn btn-outline">Cancel</button>
                        <button @click="submitLayer()" class="btn btn-primary" :disabled="submitting">
                            <span x-text="editingLayer ? 'Update Layer' : 'Create Layer'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <!-- Delete Layer Modal -->
        <template x-if="showDeleteLayerModal">
            <div class="modal-overlay" @click.self="showDeleteLayerModal = false">
                <div class="modal-content" @click.stop>
                    <div class="modal-header">
                        <h3 class="text-lg font-semibold text-gray-900">Delete Layer</h3>
                        <button @click="showDeleteLayerModal = false" class="btn-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="text-sm text-gray-600">Are you sure you want to delete Layer #<span
                                x-text="deletingLayer?.layer_order"></span>?</p>
                    </div>
                    <div class="modal-footer">
                        <button @click="showDeleteLayerModal = false" class="btn btn-outline">Cancel</button>
                        <button @click="performDeleteLayer()" class="btn btn-danger"
                            :disabled="submitting">Delete</button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script>
        function layupShow(supplierId, layupId) {
            return {
                supplierId,
                layupId,
                layup: null,
                layers: [],
                loading: true,
                submitting: false,

                // Edit Layup
                showEditLayupModal: false,
                layupForm: {
                    name: ''
                },
                layupErrors: {},

                // Layer CRUD
                showLayerModal: false,
                showDeleteLayerModal: false,
                editingLayer: null,
                deletingLayer: null,
                layerForm: {
                    layer_order: '',
                    thickness: '',
                    width: '',
                    angle: ''
                },
                layerErrors: {},

                async init() {
                    await this.fetchData();
                },

                async fetchData() {
                    this.loading = true;
                    try {
                        const [layupRes, layersRes] = await Promise.all([
                            apiRequest(`/api/suppliers/${this.supplierId}/layups/${this.layupId}`),
                            apiRequest(`/api/suppliers/${this.supplierId}/layups/${this.layupId}/layers`),
                        ]);
                        this.layup = layupRes.data;
                        this.layers = layersRes.data;
                    } catch (e) {
                        showToast('Failed to load data', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                get sortedLayers() {
                    return [...this.layers].sort((a, b) => a.layer_order - b.layer_order);
                },

                get totalThickness() {
                    return this.layers.reduce((sum, l) => sum + (parseFloat(l.thickness) || 0), 0);
                },

                formatDate(d) {
                    if (!d) return '-';
                    return new Date(d).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: '2-digit'
                    });
                },

                // Edit Layup
                openEditLayupModal() {
                    this.layupForm = {
                        name: this.layup?.name || ''
                    };
                    this.layupErrors = {};
                    this.showEditLayupModal = true;
                },

                async updateLayup() {
                    this.submitting = true;
                    this.layupErrors = {};
                    try {
                        await apiRequest(`/api/suppliers/${this.supplierId}/layups/${this.layupId}`, {
                            method: 'PUT',
                            body: JSON.stringify(this.layupForm),
                        });
                        showToast('Layup updated.');
                        this.showEditLayupModal = false;
                        await this.fetchData();
                    } catch (e) {
                        if (e.errors) this.layupErrors = e.errors;
                        else showToast(e.message || 'Error', 'error');
                    } finally {
                        this.submitting = false;
                    }
                },

                // Layer CRUD
                openCreateLayerModal() {
                    this.editingLayer = null;
                    const nextOrder = this.layers.length > 0 ?
                        Math.max(...this.layers.map(l => l.layer_order)) + 1 :
                        1;
                    this.layerForm = {
                        layer_order: nextOrder,
                        thickness: '',
                        width: '',
                        angle: ''
                    };
                    this.layerErrors = {};
                    this.showLayerModal = true;
                },

                openEditLayerModal(layer) {
                    this.editingLayer = layer;
                    this.layerForm = {
                        layer_order: layer.layer_order,
                        thickness: layer.thickness,
                        width: layer.width,
                        angle: layer.angle,
                    };
                    this.layerErrors = {};
                    this.showLayerModal = true;
                },

                async submitLayer() {
                    this.submitting = true;
                    this.layerErrors = {};
                    try {
                        if (this.editingLayer) {
                            await apiRequest(
                                `/api/suppliers/${this.supplierId}/layups/${this.layupId}/layers/${this.editingLayer.id}`, {
                                    method: 'PUT',
                                    body: JSON.stringify(this.layerForm),
                                });
                            showToast('Layer updated.');
                        } else {
                            await apiRequest(`/api/suppliers/${this.supplierId}/layups/${this.layupId}/layers`, {
                                method: 'POST',
                                body: JSON.stringify(this.layerForm),
                            });
                            showToast('Layer created.');
                        }
                        this.showLayerModal = false;
                        await this.fetchData();
                    } catch (e) {
                        if (e.errors) this.layerErrors = e.errors;
                        else showToast(e.message || 'Error', 'error');
                    } finally {
                        this.submitting = false;
                    }
                },

                confirmDeleteLayer(layer) {
                    this.deletingLayer = layer;
                    this.showDeleteLayerModal = true;
                },

                async performDeleteLayer() {
                    this.submitting = true;
                    try {
                        await apiRequest(
                            `/api/suppliers/${this.supplierId}/layups/${this.layupId}/layers/${this.deletingLayer.id}`, {
                                method: 'DELETE',
                            });
                        showToast('Layer deleted.');
                        this.showDeleteLayerModal = false;
                        await this.fetchData();
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
