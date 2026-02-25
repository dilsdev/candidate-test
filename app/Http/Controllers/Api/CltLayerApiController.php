<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCltLayerRequest;
use App\Http\Requests\UpdateCltLayerRequest;
use App\Http\Resources\CltLayerResource;
use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Services\CltLayerService;
use Illuminate\Http\JsonResponse;

class CltLayerApiController extends Controller
{
    public function __construct(protected CltLayerService $layerService) {}

    /**
     * GET /api/suppliers/{supplier}/layups/{layup}/layers
     */
    public function index(Supplier $supplier, CltLayup $layup): JsonResponse
    {
        $layers = $layup->layers()->orderBy('layer_order')->get();

        return response()->json([
            'data' => CltLayerResource::collection($layers),
        ]);
    }

    /**
     * POST /api/suppliers/{supplier}/layups/{layup}/layers
     */
    public function store(StoreCltLayerRequest $request, Supplier $supplier, CltLayup $layup): JsonResponse
    {
        $layer = $this->layerService->create(
            array_merge($request->validated(), ['layup_id' => $layup->id])
        );

        return response()->json([
            'message' => 'Layer created successfully.',
            'data' => new CltLayerResource($layer),
        ], 201);
    }

    /**
     * PUT /api/suppliers/{supplier}/layups/{layup}/layers/{layer}
     */
    public function update(UpdateCltLayerRequest $request, Supplier $supplier, CltLayup $layup, CltLayer $layer): JsonResponse
    {
        $this->layerService->update($layer->id, $request->validated());
        $layer->refresh();

        return response()->json([
            'message' => 'Layer updated successfully.',
            'data' => new CltLayerResource($layer),
        ]);
    }

    /**
     * DELETE /api/suppliers/{supplier}/layups/{layup}/layers/{layer}
     */
    public function destroy(Supplier $supplier, CltLayup $layup, CltLayer $layer): JsonResponse
    {
        $this->layerService->delete($layer->id);

        return response()->json([
            'message' => 'Layer deleted successfully.',
        ]);
    }
}
