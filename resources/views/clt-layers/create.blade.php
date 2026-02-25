<x-app-layout>
    <div x-data="createLayer({{ $supplier->id }}, {{ $layup->id }})" x-init="init()">
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
            <span class="text-gray-900 font-medium">New Layer</span>
        </nav>

        <div class="max-w-lg">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-semibold text-gray-900">Add Layer</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Add a new layer to {{ $layup->name }}.</p>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="layer_order" class="block text-sm font-medium text-gray-700 mb-1">Layer
                                Order</label>
                            <input type="number" id="layer_order" x-model.number="form.layer_order" class="input"
                                min="1" placeholder="e.g. 1" autofocus>
                            <template x-if="errors.layer_order">
                                <p class="mt-1 text-sm text-red-600" x-text="errors.layer_order[0]"></p>
                            </template>
                        </div>
                        <div>
                            <label for="angle" class="block text-sm font-medium text-gray-700 mb-1">Angle (°)</label>
                            <input type="number" id="angle" x-model.number="form.angle" class="input"
                                step="0.01" placeholder="e.g. 0 or 90">
                            <template x-if="errors.angle">
                                <p class="mt-1 text-sm text-red-600" x-text="errors.angle[0]"></p>
                            </template>
                        </div>
                        <div>
                            <label for="thickness" class="block text-sm font-medium text-gray-700 mb-1">Thickness
                                (mm)</label>
                            <input type="number" id="thickness" x-model.number="form.thickness" class="input"
                                step="0.01" min="0" placeholder="e.g. 20.00">
                            <template x-if="errors.thickness">
                                <p class="mt-1 text-sm text-red-600" x-text="errors.thickness[0]"></p>
                            </template>
                        </div>
                        <div>
                            <label for="width" class="block text-sm font-medium text-gray-700 mb-1">Width
                                (mm)</label>
                            <input type="number" id="width" x-model.number="form.width" class="input"
                                step="0.01" min="0" placeholder="e.g. 100.00">
                            <template x-if="errors.width">
                                <p class="mt-1 text-sm text-red-600" x-text="errors.width[0]"></p>
                            </template>
                        </div>
                    </div>
                    <div class="flex items-center justify-end space-x-3">
                        <a href="{{ route('suppliers.layups.layers.index', [$supplier, $layup]) }}"
                            class="btn btn-outline">Cancel</a>
                        <button @click="submit()" class="btn btn-primary" :disabled="submitting">
                            <template x-if="submitting">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg>
                            </template>
                            Create Layer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function createLayer(supplierId, layupId) {
            return {
                supplierId,
                layupId,
                form: {
                    layer_order: '',
                    thickness: '',
                    width: '',
                    angle: ''
                },
                errors: {},
                submitting: false,
                init() {},
                async submit() {
                    this.submitting = true;
                    this.errors = {};
                    try {
                        await apiRequest(`/api/suppliers/${this.supplierId}/layups/${this.layupId}/layers`, {
                            method: 'POST',
                            body: JSON.stringify(this.form),
                        });
                        showToast('Layer created successfully.');
                        window.location.href = `/suppliers/${this.supplierId}/layups/${this.layupId}`;
                    } catch (e) {
                        if (e.errors) this.errors = e.errors;
                        else showToast(e.message || 'Failed to create layer', 'error');
                    } finally {
                        this.submitting = false;
                    }
                },
            };
        }
    </script>
</x-app-layout>
