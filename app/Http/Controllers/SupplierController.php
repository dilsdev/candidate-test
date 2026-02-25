<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportSupplierRequest;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;
use App\Services\ImportExportService;
use App\Services\SupplierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SupplierController extends Controller
{
    public function __construct(
        protected SupplierService $supplierService,
        protected ImportExportService $importExportService,
    ) {}

    public function index(): View
    {
        $suppliers = $this->supplierService->getAll();

        return view('suppliers.index', compact('suppliers'));
    }

    public function create(): View
    {
        return view('suppliers.create');
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        $this->supplierService->create($request->validated());

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier): View
    {
        $supplier = $this->supplierService->getWithLayupsAndLayers($supplier->id);

        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $this->supplierService->update($supplier->id, $request->validated());

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $this->supplierService->delete($supplier->id);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }

    public function export(Supplier $supplier): StreamedResponse
    {
        $data = $this->importExportService->export($supplier);
        $filename = 'supplier_'.str_replace(' ', '_', strtolower($supplier->name)).'_export.json';

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function importForm(Supplier $supplier): View
    {
        return view('suppliers.import', compact('supplier'));
    }

    public function import(ImportSupplierRequest $request, Supplier $supplier): RedirectResponse|View
    {
        $file = $request->file('file');
        $jsonContent = file_get_contents($file->getRealPath());
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->withErrors(['file' => 'Invalid JSON file.']);
        }

        if (! isset($data['supplier']['layups'])) {
            return back()->withErrors(['file' => 'Invalid import format. Expected "supplier.layups" structure.']);
        }

        $strategy = $request->input('strategy');

        // For manual resolution, analyze first and show conflicts page
        if ($strategy === 'manual') {
            $analysis = $this->importExportService->analyzeImport($supplier, $data);

            if ($analysis['has_conflicts']) {
                // Store data in session for the conflict resolution page
                session(['import_data' => $data, 'import_analysis' => $analysis]);

                return view('suppliers.conflicts', [
                    'supplier' => $supplier,
                    'analysis' => $analysis,
                    'data' => $data,
                ]);
            }

            // No conflicts — just import directly
            $strategy = 'skip';
        }

        $result = $this->importExportService->executeImport($supplier, $data, $strategy);

        if (! $result['success']) {
            return back()->withErrors(['import' => $result['message']])
                ->with('conflicts', $result['conflicts'] ?? []);
        }

        return redirect()->route('suppliers.show', $supplier)
            ->with('success', $result['message']);
    }

    public function resolveConflicts(Request $request, Supplier $supplier): RedirectResponse
    {
        $data = session('import_data');
        $resolutions = $request->input('resolutions', []);

        if (! $data) {
            return redirect()->route('suppliers.show', $supplier)
                ->withErrors(['import' => 'Import session expired. Please try again.']);
        }

        $result = $this->importExportService->executeManualResolution($supplier, $data, $resolutions);

        session()->forget(['import_data', 'import_analysis']);

        return redirect()->route('suppliers.show', $supplier)
            ->with('success', $result['message']);
    }
}
