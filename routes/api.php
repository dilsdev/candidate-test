<?php

use App\Http\Controllers\Api\CltLayerApiController;
use App\Http\Controllers\Api\CltLayupApiController;
use App\Http\Controllers\Api\SupplierApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All API routes are prefixed with /api and support both Sanctum token
| and web session authentication.
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    // Supplier CRUD
    Route::apiResource('suppliers', SupplierApiController::class);

    // Supplier Export & Import
    Route::get('suppliers/{supplier}/export', [SupplierApiController::class, 'export'])
        ->name('api.suppliers.export');
    Route::post('suppliers/{supplier}/import', [SupplierApiController::class, 'import'])
        ->name('api.suppliers.import');
    Route::post('suppliers/{supplier}/resolve-conflicts', [SupplierApiController::class, 'resolveConflicts'])
        ->name('api.suppliers.resolve-conflicts');

    // CLT Layup CRUD (nested under supplier)
    Route::apiResource('suppliers.layups', CltLayupApiController::class);

    // CLT Layer CRUD (nested under supplier.layup)
    Route::apiResource('suppliers.layups.layers', CltLayerApiController::class)->except(['show']);
});
