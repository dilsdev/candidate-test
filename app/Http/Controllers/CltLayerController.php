<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCltLayerRequest;
use App\Http\Requests\UpdateCltLayerRequest;
use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Services\CltLayerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CltLayerController extends Controller
{
    public function __construct(protected CltLayerService $layerService)
    {
    }

    public function index(Supplier $supplier, CltLayup $layup): View
    {
        $layers = $this->layerService->getAllForLayup($layup->id);
        return view('clt-layers.index', compact('supplier', 'layup', 'layers'));
    }

    public function create(Supplier $supplier, CltLayup $layup): View
    {
        return view('clt-layers.create', compact('supplier', 'layup'));
    }

    public function store(StoreCltLayerRequest $request, Supplier $supplier, CltLayup $layup): RedirectResponse
    {
        $this->layerService->create(array_merge(
            $request->validated(),
            ['layup_id' => $layup->id]
        ));

        return redirect()->route('suppliers.layups.layers.index', [$supplier, $layup])
            ->with('success', 'CLT Layer created successfully.');
    }

    public function edit(Supplier $supplier, CltLayup $layup, CltLayer $layer): View
    {
        return view('clt-layers.edit', compact('supplier', 'layup', 'layer'));
    }

    public function update(UpdateCltLayerRequest $request, Supplier $supplier, CltLayup $layup, CltLayer $layer): RedirectResponse
    {
        $this->layerService->update($layer->id, $request->validated());

        return redirect()->route('suppliers.layups.layers.index', [$supplier, $layup])
            ->with('success', 'CLT Layer updated successfully.');
    }

    public function destroy(Supplier $supplier, CltLayup $layup, CltLayer $layer): RedirectResponse
    {
        $this->layerService->delete($layer->id);

        return redirect()->route('suppliers.layups.layers.index', [$supplier, $layup])
            ->with('success', 'CLT Layer deleted successfully.');
    }
}
