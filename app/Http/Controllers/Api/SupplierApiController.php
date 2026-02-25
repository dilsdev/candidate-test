<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use App\Services\ImportExportService;
use App\Services\SupplierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierApiController extends Controller
{
    public function __construct(
        protected SupplierService $supplierService,
        protected ImportExportService $importExportService,
    ) {
    }

    /**
     * GET /api/suppliers
     */
    public function index(): JsonResponse
    {
        $suppliers = Supplier::withCount('layups')->get();
        return response()->json([
            'data' => SupplierResource::collection($suppliers),
        ]);
    }

    /**
     * POST /api/suppliers
     */
    public function store(StoreSupplierRequest $request): JsonResponse
    {
        $supplier = $this->supplierService->create($request->validated());
        return response()->json([
            'message' => 'Supplier created successfully.',
            'data' => new SupplierResource($supplier),
        ], 201);
    }

    /**
     * GET /api/suppliers/{supplier}
     */
    public function show(Supplier $supplier): JsonResponse
    {
        $supplier->load('layups.layers');
        $supplier->loadCount('layups');
        return response()->json([
            'data' => new SupplierResource($supplier),
        ]);
    }

    /**
     * PUT /api/suppliers/{supplier}
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier): JsonResponse
    {
        $this->supplierService->update($supplier->id, $request->validated());
        $supplier->refresh();
        return response()->json([
            'message' => 'Supplier updated successfully.',
            'data' => new SupplierResource($supplier),
        ]);
    }

    /**
     * DELETE /api/suppliers/{supplier}
     */
    public function destroy(Supplier $supplier): JsonResponse
    {
        $this->supplierService->delete($supplier->id);
        return response()->json([
            'message' => 'Supplier deleted successfully.',
        ]);
    }

    /**
     * GET /api/suppliers/{supplier}/export
     */
    public function export(Supplier $supplier): JsonResponse
    {
        $data = $this->importExportService->export($supplier);
        return response()->json($data);
    }

    /**
     * POST /api/suppliers/{supplier}/import
     *
     * Accepts JSON body: { "strategy": "overwrite|skip|duplicate|reject|manual", "data": { "supplier": { ... } } }
     * Or a file upload with 'file' field and 'strategy' field.
     */
    public function import(Request $request, Supplier $supplier): JsonResponse
    {
        $request->validate([
            'strategy' => 'required|in:overwrite,skip,duplicate,reject,manual',
        ]);

        // Accept either JSON body or file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $jsonContent = file_get_contents($file->getRealPath());
            $data = json_decode($jsonContent, true);
        } else {
            $data = $request->input('data');
        }

        if (!$data || !isset($data['supplier']['layups'])) {
            return response()->json([
                'message' => 'Invalid import format. Expected "supplier.layups" structure.',
                'errors' => ['data' => ['Invalid import data structure.']],
            ], 422);
        }

        $strategy = $request->input('strategy');

        // For manual strategy, return analysis of conflicts
        if ($strategy === 'manual') {
            $analysis = $this->importExportService->analyzeImport($supplier, $data);

            if ($analysis['has_conflicts']) {
                return response()->json([
                    'message' => 'Conflicts detected. Resolve them using the resolutions endpoint.',
                    'has_conflicts' => true,
                    'analysis' => $analysis,
                    'data' => $data,
                ], 409);
            }

            // No conflicts — just import directly
            $strategy = 'skip';
        }

        $result = $this->importExportService->executeImport($supplier, $data, $strategy);

        if (!$result['success']) {
            return response()->json([
                'message' => $result['message'],
                'conflicts' => $result['conflicts'] ?? [],
            ], 409);
        }

        return response()->json([
            'message' => $result['message'],
        ]);
    }

    /**
     * POST /api/suppliers/{supplier}/resolve-conflicts
     *
     * Accepts JSON body with:
     * - data: the original import data
     * - resolutions: array of { existing_layer_id, action, incoming: { thickness, width, angle } }
     */
    public function resolveConflicts(Request $request, Supplier $supplier): JsonResponse
    {
        $request->validate([
            'data' => 'required|array',
            'data.supplier.layups' => 'required|array',
            'resolutions' => 'required|array',
        ]);

        $data = $request->input('data');
        $resolutions = $request->input('resolutions', []);

        $result = $this->importExportService->executeManualResolution($supplier, $data, $resolutions);

        return response()->json([
            'message' => $result['message'],
        ]);
    }
}
