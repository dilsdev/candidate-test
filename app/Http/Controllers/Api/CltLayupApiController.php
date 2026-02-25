<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCltLayupRequest;
use App\Http\Requests\UpdateCltLayupRequest;
use App\Http\Resources\CltLayupResource;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Services\CltLayupService;
use Illuminate\Http\JsonResponse;

class CltLayupApiController extends Controller
{
    public function __construct(protected CltLayupService $layupService)
    {
    }

    /**
     * GET /api/suppliers/{supplier}/layups
     */
    public function index(Supplier $supplier): JsonResponse
    {
        $layups = $supplier->layups()->withCount('layers')->get();
        return response()->json([
            'data' => CltLayupResource::collection($layups),
        ]);
    }

    /**
     * POST /api/suppliers/{supplier}/layups
     */
    public function store(StoreCltLayupRequest $request, Supplier $supplier): JsonResponse
    {
        $layup = $this->layupService->create(
            array_merge($request->validated(), ['supplier_id' => $supplier->id])
        );
        return response()->json([
            'message' => 'Layup created successfully.',
            'data' => new CltLayupResource($layup),
        ], 201);
    }

    /**
     * GET /api/suppliers/{supplier}/layups/{layup}
     */
    public function show(Supplier $supplier, CltLayup $layup): JsonResponse
    {
        $layup->load('layers');
        $layup->loadCount('layers');
        return response()->json([
            'data' => new CltLayupResource($layup),
        ]);
    }

    /**
     * PUT /api/suppliers/{supplier}/layups/{layup}
     */
    public function update(UpdateCltLayupRequest $request, Supplier $supplier, CltLayup $layup): JsonResponse
    {
        $this->layupService->update($layup->id, $request->validated());
        $layup->refresh();
        return response()->json([
            'message' => 'Layup updated successfully.',
            'data' => new CltLayupResource($layup),
        ]);
    }

    /**
     * DELETE /api/suppliers/{supplier}/layups/{layup}
     */
    public function destroy(Supplier $supplier, CltLayup $layup): JsonResponse
    {
        $this->layupService->delete($layup->id);
        return response()->json([
            'message' => 'Layup deleted successfully.',
        ]);
    }
}
