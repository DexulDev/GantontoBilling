<?php

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\InvoiceItemController;

// API Routes (protegidas por autenticación)
Route::middleware(['auth:sanctum'])->group(function () {
    // API para autocompletar items al crear facturas
    Route::get('/items/search', function (Request $request) {
        $query = $request->input('query');
        $items = Item::where('user_id', Auth::id())
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                    ->orWhere('description', 'like', "%$query%")
                    ->orWhere('sku', 'like', "%$query%");
            })
            ->where('is_active', true)
            ->limit(10)
            ->get(['id', 'name', 'description', 'price', 'tax_percent']);

        return response()->json($items);
    })->name('api.items.search');

    // Aquí puedes agregar más rutas de API relacionadas con items
});

// Rutas para webhooks (no requieren autenticación)
Route::post('/webhooks/stripe', [App\Http\Controllers\WebhookController::class, 'handleStripeWebhook'])
    ->name('webhooks.stripe');

// Ruta para webhooks de Stripe (no requiere autenticación)
Route::post('/webhooks/stripe', [WebhookController::class, 'handleStripeWebhook'])
    ->name('webhooks.stripe');

// Tus otras rutas API con autenticación
Route::middleware('auth:sanctum')->group(function () {
    // Rutas API existentes
});
