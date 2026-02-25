<x-app-layout>
    <div x-data="conflictResolver()" x-init="init()">
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
            <span class="text-gray-900 font-medium">Resolve Conflicts</span>
        </nav>

        <!-- Summary Banner -->
        <div class="alert alert-warning mb-6">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            <div>
                <p class="font-medium">
                    Found <span x-text="totalConflicts"></span> conflict(s) across {{ count($analysis['conflicts']) }}
                    layup(s).
                </p>
                <p class="text-sm mt-0.5">
                    Viewing conflict <span x-text="currentIndex + 1"></span> of <span x-text="totalConflicts"></span>.
                    Resolved: <span x-text="resolvedCount"></span>/<span x-text="totalConflicts"></span>.
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('suppliers.resolve-conflicts', $supplier) }}">
            @csrf

            <!-- Conflict Navigator -->
            <template x-for="(conflict, conflictIndex) in conflicts" :key="conflictIndex">
                <div x-show="isCurrentConflict(conflictIndex)" class="mb-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Layup: <span x-text="conflict.layup_name" class="text-green-800"></span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <template x-for="(layerConflict, layerIndex) in conflict.layer_conflicts"
                                :key="layerIndex">
                                <div x-show="isCurrentLayer(conflictIndex, layerIndex)"
                                    class="border border-gray-200 rounded-lg overflow-hidden">
                                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                        <span class="text-sm font-medium text-gray-700">
                                            Layer Order: <span x-text="layerConflict.layer_order"
                                                class="font-bold"></span>
                                        </span>
                                    </div>

                                    <!-- Side-by-side comparison -->
                                    <div class="grid grid-cols-2 divide-x divide-gray-200">
                                        <!-- Existing (Current) -->
                                        <div class="p-4">
                                            <h4 class="text-sm font-semibold text-red-600 mb-3 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M20 12H4" />
                                                </svg>
                                                Current Data (Existing)
                                            </h4>
                                            <div class="space-y-2">
                                                <template x-for="field in ['thickness', 'width', 'angle']"
                                                    :key="field">
                                                    <div class="flex justify-between items-center py-1 px-2 rounded"
                                                        :class="layerConflict.diffs[field] ? 'bg-red-50 border border-red-200' :
                                                            'bg-gray-50'">
                                                        <span class="text-xs font-medium text-gray-500 uppercase"
                                                            x-text="field"></span>
                                                        <span class="text-sm font-mono"
                                                            x-text="layerConflict.existing[field]"
                                                            :class="layerConflict.diffs[field] ? 'text-red-700 font-bold' :
                                                                'text-gray-700'"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- Incoming (Imported) -->
                                        <div class="p-4">
                                            <h4 class="text-sm font-semibold text-green-600 mb-3 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                Incoming Data (Import)
                                            </h4>
                                            <div class="space-y-2">
                                                <template x-for="field in ['thickness', 'width', 'angle']"
                                                    :key="field">
                                                    <div class="flex justify-between items-center py-1 px-2 rounded"
                                                        :class="layerConflict.diffs[field] ?
                                                            'bg-green-50 border border-green-200' : 'bg-gray-50'">
                                                        <span class="text-xs font-medium text-gray-500 uppercase"
                                                            x-text="field"></span>
                                                        <span class="text-sm font-mono"
                                                            x-text="layerConflict.incoming[field]"
                                                            :class="layerConflict.diffs[field] ? 'text-green-700 font-bold' :
                                                                'text-gray-700'"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
                                        <div class="flex items-center justify-center space-x-4">
                                            <button type="button"
                                                @click="resolveConflict(conflictIndex, layerIndex, 'keep_existing')"
                                                class="btn btn-sm"
                                                :class="getResolution(conflictIndex, layerIndex) === 'keep_existing' ?
                                                    'btn-danger' : 'btn-outline'">
                                                Keep Existing
                                            </button>
                                            <button type="button"
                                                @click="resolveConflict(conflictIndex, layerIndex, 'accept_incoming')"
                                                class="btn btn-sm"
                                                :class="getResolution(conflictIndex, layerIndex) === 'accept_incoming' ?
                                                    'btn-primary' : 'btn-outline'">
                                                Accept Incoming
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Hidden resolution inputs -->
            <template x-for="(res, index) in resolutionsList" :key="index">
                <div>
                    <input type="hidden" :name="'resolutions[' + index + '][existing_layer_id]'"
                        :value="res.existing_layer_id">
                    <input type="hidden" :name="'resolutions[' + index + '][action]'" :value="res.action">
                    <input type="hidden" :name="'resolutions[' + index + '][incoming][thickness]'"
                        :value="res.incoming.thickness">
                    <input type="hidden" :name="'resolutions[' + index + '][incoming][width]'"
                        :value="res.incoming.width">
                    <input type="hidden" :name="'resolutions[' + index + '][incoming][angle]'"
                        :value="res.incoming.angle">
                </div>
            </template>

            <!-- Navigation & Submit -->
            <div class="flex items-center justify-between mt-6">
                <div class="flex space-x-2">
                    <button type="button" @click="prev()" :disabled="currentIndex === 0"
                        class="btn btn-outline btn-sm">
                        ← Previous
                    </button>
                    <button type="button" @click="next()" :disabled="currentIndex >= totalConflicts - 1"
                        class="btn btn-outline btn-sm">
                        Next →
                    </button>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-outline">Cancel Import</a>
                    <button type="submit" :disabled="!allResolved" class="btn btn-primary">
                        Apply Resolutions (<span x-text="resolvedCount"></span>/<span x-text="totalConflicts"></span>)
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function conflictResolver() {
            const conflicts = @json($analysis['conflicts']);

            let flatConflicts = [];
            conflicts.forEach((conflict, conflictIndex) => {
                conflict.layer_conflicts.forEach((layer, layerIndex) => {
                    flatConflicts.push({
                        conflictIndex,
                        layerIndex,
                        layer,
                        conflict
                    });
                });
            });

            return {
                conflicts: conflicts,
                flatConflicts: flatConflicts,
                currentIndex: 0,
                totalConflicts: flatConflicts.length,
                resolutions: {},

                init() {},

                isCurrentConflict(conflictIndex) {
                    return this.flatConflicts[this.currentIndex]?.conflictIndex === conflictIndex;
                },

                isCurrentLayer(conflictIndex, layerIndex) {
                    const current = this.flatConflicts[this.currentIndex];
                    return current?.conflictIndex === conflictIndex && current?.layerIndex === layerIndex;
                },

                resolveConflict(conflictIndex, layerIndex, action) {
                    const key = conflictIndex + '-' + layerIndex;
                    this.resolutions[key] = {
                        action: action,
                        existing_layer_id: this.conflicts[conflictIndex].layer_conflicts[layerIndex].existing_layer_id,
                        incoming: this.conflicts[conflictIndex].layer_conflicts[layerIndex].incoming,
                    };
                },

                getResolution(conflictIndex, layerIndex) {
                    const key = conflictIndex + '-' + layerIndex;
                    return this.resolutions[key]?.action || null;
                },

                get resolvedCount() {
                    return Object.keys(this.resolutions).length;
                },

                get allResolved() {
                    return this.resolvedCount === this.totalConflicts;
                },

                get resolutionsList() {
                    return Object.values(this.resolutions);
                },

                prev() {
                    if (this.currentIndex > 0) this.currentIndex--;
                },

                next() {
                    if (this.currentIndex < this.totalConflicts - 1) this.currentIndex++;
                },
            };
        }
    </script>
</x-app-layout>
