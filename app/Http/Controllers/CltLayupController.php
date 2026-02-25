<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCltLayupRequest;
use App\Http\Requests\UpdateCltLayupRequest;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Services\CltLayupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CltLayupController extends Controller
{
    public function __construct(protected CltLayupService $layupService)
    {
    }

    public function index(Supplier $supplier): View
    {
        $layups = $this->layupService->getAllForSupplier($supplier->id);
        return view('clt-layups.index', compact('supplier', 'layups'));
    }

    public function create(Supplier $supplier): View
    {
        return view('clt-layups.create', compact('supplier'));
    }

    public function store(StoreCltLayupRequest $request, Supplier $supplier): RedirectResponse
    {
        $this->layupService->create(array_merge(
            $request->validated(),
            ['supplier_id' => $supplier->id]
        ));

        return redirect()->route('suppliers.layups.index', $supplier)
            ->with('success', 'CLT Layup created successfully.');
    }

    public function show(Supplier $supplier, CltLayup $layup): View
    {
        $layup = $this->layupService->getWithLayers($layup->id);
        return view('clt-layups.show', compact('supplier', 'layup'));
    }

    public function edit(Supplier $supplier, CltLayup $layup): View
    {
        return view('clt-layups.edit', compact('supplier', 'layup'));
    }

    public function update(UpdateCltLayupRequest $request, Supplier $supplier, CltLayup $layup): RedirectResponse
    {
        $this->layupService->update($layup->id, $request->validated());

        return redirect()->route('suppliers.layups.index', $supplier)
            ->with('success', 'CLT Layup updated successfully.');
    }

    public function destroy(Supplier $supplier, CltLayup $layup): RedirectResponse
    {
        $this->layupService->delete($layup->id);

        return redirect()->route('suppliers.layups.index', $supplier)
            ->with('success', 'CLT Layup deleted successfully.');
    }
}
