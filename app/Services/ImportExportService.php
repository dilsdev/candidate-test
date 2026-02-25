<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\CltLayup;
use App\Models\CltLayer;
use App\Repositories\Contracts\CltLayupRepositoryInterface;
use App\Repositories\Contracts\CltLayerRepositoryInterface;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ImportExportService
{
    public function __construct(
        protected SupplierRepositoryInterface $supplierRepository,
        protected CltLayupRepositoryInterface $layupRepository,
        protected CltLayerRepositoryInterface $layerRepository,
    ) {
    }

    /**
     * Export a supplier with all its layups and layers as a nested array.
     */
    public function export(Supplier $supplier): array
    {
        $supplier = $this->supplierRepository->getWithLayupsAndLayers($supplier->id);

        return [
            'supplier' => [
                'name' => $supplier->name,
                'layups' => $supplier->layups->map(function ($layup) {
                    return [
                        'name' => $layup->name,
                        'layers' => $layup->layers->map(function ($layer) {
                            return [
                                'layer_order' => $layer->layer_order,
                                'thickness' => (float) $layer->thickness,
                                'width' => (float) $layer->width,
                                'angle' => (float) $layer->angle,
                            ];
                        })->toArray(),
                    ];
                })->toArray(),
            ],
        ];
    }

    /**
     * Analyze import data and detect conflicts.
     *
     * Returns:
     * [
     *   'has_conflicts' => bool,
     *   'conflicts' => [...],
     *   'new_layups' => [...],
     *   'matching_layups' => [...],
     * ]
     */
    public function analyzeImport(Supplier $supplier, array $data): array
    {
        $importedLayups = $data['supplier']['layups'] ?? [];
        $conflicts = [];
        $newLayups = [];
        $matchingLayups = [];

        foreach ($importedLayups as $layupIndex => $importedLayup) {
            $existingLayup = $this->layupRepository->findByNameAndSupplier(
                $importedLayup['name'],
                $supplier->id
            );

            if (!$existingLayup) {
                // Brand new layup — no conflict
                $newLayups[] = $importedLayup;
                continue;
            }

            // Layup name matches — check layers for conflicts
            $layupConflicts = [];
            $importedLayers = $importedLayup['layers'] ?? [];

            foreach ($importedLayers as $layerIndex => $importedLayer) {
                $existingLayer = $this->layerRepository->findByOrderAndLayup(
                    $importedLayer['layer_order'],
                    $existingLayup->id
                );

                if (!$existingLayer) {
                    // New layer — no conflict
                    continue;
                }

                // Check if any fields differ
                $diffs = [];
                foreach (['thickness', 'width', 'angle'] as $field) {
                    if ((float) $existingLayer->$field !== (float) $importedLayer[$field]) {
                        $diffs[$field] = [
                            'existing' => (float) $existingLayer->$field,
                            'incoming' => (float) $importedLayer[$field],
                        ];
                    }
                }

                if (!empty($diffs)) {
                    $layupConflicts[] = [
                        'layer_order' => $importedLayer['layer_order'],
                        'existing_layer_id' => $existingLayer->id,
                        'incoming' => $importedLayer,
                        'existing' => [
                            'layer_order' => $existingLayer->layer_order,
                            'thickness' => (float) $existingLayer->thickness,
                            'width' => (float) $existingLayer->width,
                            'angle' => (float) $existingLayer->angle,
                        ],
                        'diffs' => $diffs,
                    ];
                }
            }

            if (!empty($layupConflicts)) {
                $conflicts[] = [
                    'layup_name' => $importedLayup['name'],
                    'existing_layup_id' => $existingLayup->id,
                    'imported_layup' => $importedLayup,
                    'layer_conflicts' => $layupConflicts,
                ];
            } else {
                $matchingLayups[] = [
                    'existing_layup_id' => $existingLayup->id,
                    'imported_layup' => $importedLayup,
                ];
            }
        }

        return [
            'has_conflicts' => !empty($conflicts),
            'conflicts' => $conflicts,
            'new_layups' => $newLayups,
            'matching_layups' => $matchingLayups,
        ];
    }

    /**
     * Execute the import with the specified strategy.
     *
     * Strategies: overwrite, skip, duplicate, reject
     */
    public function executeImport(Supplier $supplier, array $data, string $strategy): array
    {
        $analysis = $this->analyzeImport($supplier, $data);

        if ($strategy === 'reject' && $analysis['has_conflicts']) {
            return [
                'success' => false,
                'message' => 'Import rejected due to conflicts.',
                'conflicts' => $analysis['conflicts'],
            ];
        }

        return DB::transaction(function () use ($supplier, $data, $strategy, $analysis) {
            $created = 0;
            $updated = 0;
            $skipped = 0;

            // 1. Create brand new layups (no conflicts)
            foreach ($analysis['new_layups'] as $layupData) {
                $layup = $this->layupRepository->create([
                    'supplier_id' => $supplier->id,
                    'name' => $layupData['name'],
                ]);
                $created++;

                foreach ($layupData['layers'] ?? [] as $layerData) {
                    $this->layerRepository->create([
                        'layup_id' => $layup->id,
                        'layer_order' => $layerData['layer_order'],
                        'thickness' => $layerData['thickness'],
                        'width' => $layerData['width'],
                        'angle' => $layerData['angle'],
                    ]);
                    $created++;
                }
            }

            // 2. Handle matching layups (same name, no layer conflicts)
            foreach ($analysis['matching_layups'] as $match) {
                $existingLayupId = $match['existing_layup_id'];
                $importedLayup = $match['imported_layup'];

                foreach ($importedLayup['layers'] ?? [] as $layerData) {
                    $existingLayer = $this->layerRepository->findByOrderAndLayup(
                        $layerData['layer_order'],
                        $existingLayupId
                    );

                    if (!$existingLayer) {
                        $this->layerRepository->create([
                            'layup_id' => $existingLayupId,
                            'layer_order' => $layerData['layer_order'],
                            'thickness' => $layerData['thickness'],
                            'width' => $layerData['width'],
                            'angle' => $layerData['angle'],
                        ]);
                        $created++;
                    }
                }
            }

            // 3. Handle conflicts based on strategy
            foreach ($analysis['conflicts'] as $conflict) {
                switch ($strategy) {
                    case 'overwrite':
                        foreach ($conflict['layer_conflicts'] as $layerConflict) {
                            $this->layerRepository->update($layerConflict['existing_layer_id'], [
                                'thickness' => $layerConflict['incoming']['thickness'],
                                'width' => $layerConflict['incoming']['width'],
                                'angle' => $layerConflict['incoming']['angle'],
                            ]);
                            $updated++;
                        }
                        // Also create new layers that don't conflict
                        $this->createNonConflictingLayers($conflict, $supplier);
                        break;

                    case 'skip':
                        $skipped += count($conflict['layer_conflicts']);
                        // Still create new layers that don't conflict
                        $this->createNonConflictingLayers($conflict, $supplier);
                        break;

                    case 'duplicate':
                        $newLayup = $this->layupRepository->create([
                            'supplier_id' => $supplier->id,
                            'name' => $conflict['layup_name'] . ' (imported)',
                        ]);
                        $created++;

                        foreach ($conflict['imported_layup']['layers'] ?? [] as $layerData) {
                            $this->layerRepository->create([
                                'layup_id' => $newLayup->id,
                                'layer_order' => $layerData['layer_order'],
                                'thickness' => $layerData['thickness'],
                                'width' => $layerData['width'],
                                'angle' => $layerData['angle'],
                            ]);
                            $created++;
                        }
                        break;
                }
            }

            return [
                'success' => true,
                'message' => "Import completed. Created: {$created}, Updated: {$updated}, Skipped: {$skipped}.",
                'created' => $created,
                'updated' => $updated,
                'skipped' => $skipped,
            ];
        });
    }

    /**
     * Execute manual (per-conflict) resolution.
     *
     * $resolutions is an array of:
     * [ 'existing_layer_id' => int, 'action' => 'keep_existing'|'accept_incoming', 'incoming' => [...] ]
     */
    public function executeManualResolution(Supplier $supplier, array $data, array $resolutions): array
    {
        $analysis = $this->analyzeImport($supplier, $data);

        return DB::transaction(function () use ($supplier, $analysis, $resolutions) {
            $updated = 0;
            $skipped = 0;
            $created = 0;

            // Create new layups
            foreach ($analysis['new_layups'] as $layupData) {
                $layup = $this->layupRepository->create([
                    'supplier_id' => $supplier->id,
                    'name' => $layupData['name'],
                ]);
                $created++;

                foreach ($layupData['layers'] ?? [] as $layerData) {
                    $this->layerRepository->create([
                        'layup_id' => $layup->id,
                        'layer_order' => $layerData['layer_order'],
                        'thickness' => $layerData['thickness'],
                        'width' => $layerData['width'],
                        'angle' => $layerData['angle'],
                    ]);
                    $created++;
                }
            }

            // Handle matching layups (no conflicts)
            foreach ($analysis['matching_layups'] as $match) {
                foreach ($match['imported_layup']['layers'] ?? [] as $layerData) {
                    $existingLayer = $this->layerRepository->findByOrderAndLayup(
                        $layerData['layer_order'],
                        $match['existing_layup_id']
                    );
                    if (!$existingLayer) {
                        $this->layerRepository->create([
                            'layup_id' => $match['existing_layup_id'],
                            'layer_order' => $layerData['layer_order'],
                            'thickness' => $layerData['thickness'],
                            'width' => $layerData['width'],
                            'angle' => $layerData['angle'],
                        ]);
                        $created++;
                    }
                }
            }

            // Apply manual resolutions
            foreach ($resolutions as $resolution) {
                if ($resolution['action'] === 'accept_incoming') {
                    $this->layerRepository->update($resolution['existing_layer_id'], [
                        'thickness' => $resolution['incoming']['thickness'],
                        'width' => $resolution['incoming']['width'],
                        'angle' => $resolution['incoming']['angle'],
                    ]);
                    $updated++;
                } else {
                    $skipped++;
                }
            }

            // Also create non-conflicting layers within conflicting layups
            foreach ($analysis['conflicts'] as $conflict) {
                $this->createNonConflictingLayers($conflict, null, $conflict['existing_layup_id']);
            }

            return [
                'success' => true,
                'message' => "Import completed. Created: {$created}, Updated: {$updated}, Skipped: {$skipped}.",
                'created' => $created,
                'updated' => $updated,
                'skipped' => $skipped,
            ];
        });
    }

    /**
     * Create layers from an imported layup that don't conflict with existing ones.
     */
    protected function createNonConflictingLayers(array $conflict, ?Supplier $supplier = null, ?int $layupId = null): void
    {
        $existingLayupId = $layupId ?? $conflict['existing_layup_id'];
        $conflictingOrders = array_column($conflict['layer_conflicts'], 'layer_order');

        foreach ($conflict['imported_layup']['layers'] ?? [] as $layerData) {
            if (!in_array($layerData['layer_order'], $conflictingOrders)) {
                $existingLayer = $this->layerRepository->findByOrderAndLayup(
                    $layerData['layer_order'],
                    $existingLayupId
                );
                if (!$existingLayer) {
                    $this->layerRepository->create([
                        'layup_id' => $existingLayupId,
                        'layer_order' => $layerData['layer_order'],
                        'thickness' => $layerData['thickness'],
                        'width' => $layerData['width'],
                        'angle' => $layerData['angle'],
                    ]);
                }
            }
        }
    }
}
