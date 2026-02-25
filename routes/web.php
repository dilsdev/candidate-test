<?php

use App\Http\Controllers\CltLayerController;
use App\Http\Controllers\CltLayupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Supplier routes
    Route::resource('suppliers', SupplierController::class);

    // Export & Import routes
    Route::get('suppliers/{supplier}/export', [SupplierController::class, 'export'])->name('suppliers.export');
    Route::get('suppliers/{supplier}/import', [SupplierController::class, 'importForm'])->name('suppliers.import.form');
    Route::post('suppliers/{supplier}/import', [SupplierController::class, 'import'])->name('suppliers.import');
    Route::post('suppliers/{supplier}/resolve-conflicts', [SupplierController::class, 'resolveConflicts'])->name('suppliers.resolve-conflicts');

    // CLT Layup routes (nested under supplier)
    Route::resource('suppliers.layups', CltLayupController::class);

    // CLT Layer routes (nested under supplier.layup)
    Route::resource('suppliers.layups.layers', CltLayerController::class)->except(['show']);
});

require __DIR__.'/auth.php';
