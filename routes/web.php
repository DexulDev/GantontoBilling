<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\InvoiceItemController;

// Rutas para Items (catálogo de productos/servicios)
Route::middleware(['auth'])->group(function () {
    // Rutas CRUD para Items (con vistas)
    Route::resource('items', ItemController::class);

    // Rutas para InvoiceItems (líneas de factura)
    Route::post('invoices/{invoice}/items', [InvoiceItemController::class, 'store'])
        ->name('invoice-items.store');

    Route::put('invoices/{invoice}/items/{item}', [InvoiceItemController::class, 'update'])
        ->name('invoice-items.update');

    Route::delete('invoices/{invoice}/items/{item}', [InvoiceItemController::class, 'destroy'])
        ->name('invoice-items.destroy');
});

