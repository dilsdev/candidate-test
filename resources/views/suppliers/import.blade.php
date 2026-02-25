<x-app-layout>
    <div x-data="importSupplier({{ $supplier->id }})" x-init="init()">
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
            <span class="text-gray-900 font-medium">Import Data</span>
        </nav>

        <div class="max-w-2xl">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-semibold text-gray-900">Import Data</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Import layup and layer data from a JSON file.</p>
                </div>
                <div class="card-body">
                    <!-- File Upload Area -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload File</label>
                        <div class="upload-area" :class="{ 'drag-over': dragOver }" @dragover.prevent="dragOver = true"
                            @dragleave.prevent="dragOver = false" @drop.prevent="handleDrop($event)"
                            @click="$refs.fileInput.click()">
                            <input type="file" x-ref="fileInput" accept=".json" class="hidden"
                                @change="handleFileSelect($event)">
                            <template x-if="!selectedFile">
                                <div>
                                    <svg class="mx-auto h-10 w-10 text-gray-300 mb-3" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="text-sm text-gray-600">Drag and drop your JSON file here, or <span
                                            class="text-green-700 font-medium">browse</span></p>
                                    <p class="text-xs text-gray-400 mt-1">Supports .json files</p>
                                </div>
                            </template>
                            <template x-if="selectedFile">
                                <div class="flex items-center justify-center space-x-3">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <div class="text-left">
                                        <p class="text-sm font-medium text-gray-900" x-text="selectedFile.name"></p>
                                        <p class="text-xs text-gray-500" x-text="formatSize(selectedFile.size)"></p>
                                    </div>
                                    <button @click.stop="removeFile()" class="btn-icon hover:!text-red-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <template x-if="fileError">
                            <p class="mt-1 text-sm text-red-600" x-text="fileError"></p>
                        </template>
                    </div>

                    <!-- Conflict Resolution Strategy -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Conflict Resolution Strategy</label>
                        <select x-model="strategy" class="input">
                            <option value="skip">Skip — Keep existing, add only new</option>
                            <option value="overwrite">Overwrite — Replace existing with imported</option>
                            <option value="duplicate">Duplicate — Create copies of conflicting items</option>
                            <option value="reject">Reject — Cancel import if conflicts found</option>
                            <option value="manual">Manual — Review conflicts individually</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-400">Choose how to handle conflicts when imported data matches
                            existing records.</p>
                    </div>

                    <!-- Warning -->
                    <div class="alert alert-warning mb-6">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <div>
                            <p class="font-medium">Potential Data Impact</p>
                            <p class="text-sm mt-0.5">Importing data may modify existing layups and layers. Make sure
                                you have exported a backup before proceeding.</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-3">
                        <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-outline">Cancel</a>
                        <button @click="submit()" class="btn btn-primary" :disabled="!selectedFile || submitting">
                            <template x-if="submitting">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg>
                            </template>
                            Confirm Import
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function importSupplier(supplierId) {
            return {
                supplierId: supplierId,
                selectedFile: null,
                fileError: '',
                strategy: 'skip',
                dragOver: false,
                submitting: false,

                init() {},

                handleDrop(event) {
                    this.dragOver = false;
                    const file = event.dataTransfer.files[0];
                    if (file) this.setFile(file);
                },

                handleFileSelect(event) {
                    const file = event.target.files[0];
                    if (file) this.setFile(file);
                },

                setFile(file) {
                    if (!file.name.endsWith('.json')) {
                        this.fileError = 'Please select a JSON file.';
                        return;
                    }
                    this.fileError = '';
                    this.selectedFile = file;
                },

                removeFile() {
                    this.selectedFile = null;
                    this.fileError = '';
                    if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                },

                formatSize(bytes) {
                    if (bytes < 1024) return bytes + ' B';
                    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
                    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
                },

                async submit() {
                    if (!this.selectedFile) return;
                    this.submitting = true;

                    try {
                        const text = await this.selectedFile.text();
                        const data = JSON.parse(text);

                        if (!data.supplier || !data.supplier.layups) {
                            this.fileError = 'Invalid import format. Expected "supplier.layups" structure.';
                            this.submitting = false;
                            return;
                        }

                        // Use form submission for manual strategy (needs server-side conflict page)
                        if (this.strategy === 'manual') {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `/suppliers/${this.supplierId}/import`;
                            form.enctype = 'multipart/form-data';

                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                            form.appendChild(csrfInput);

                            const strategyInput = document.createElement('input');
                            strategyInput.type = 'hidden';
                            strategyInput.name = 'strategy';
                            strategyInput.value = 'manual';
                            form.appendChild(strategyInput);

                            const fileInput = document.createElement('input');
                            fileInput.type = 'file';
                            fileInput.name = 'file';
                            fileInput.className = 'hidden';

                            const dt = new DataTransfer();
                            dt.items.add(this.selectedFile);
                            fileInput.files = dt.files;
                            form.appendChild(fileInput);

                            document.body.appendChild(form);
                            form.submit();
                            return;
                        }

                        // API-based import for other strategies
                        const result = await apiRequest(`/api/suppliers/${this.supplierId}/import`, {
                            method: 'POST',
                            body: JSON.stringify({
                                strategy: this.strategy,
                                data: data,
                            }),
                        });

                        showToast(result.message || 'Import completed successfully.');
                        window.location.href = `/suppliers/${this.supplierId}`;
                    } catch (e) {
                        if (e instanceof SyntaxError) {
                            this.fileError = 'Invalid JSON file.';
                        } else if (e.status === 409) {
                            showToast(e.message || 'Import rejected due to conflicts.', 'error');
                        } else {
                            showToast(e.message || 'Import failed.', 'error');
                        }
                    } finally {
                        this.submitting = false;
                    }
                },
            };
        }
    </script>
</x-app-layout>
