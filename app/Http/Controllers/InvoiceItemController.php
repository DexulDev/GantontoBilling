<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceItemController extends Controller
{
    /*
     * Crear un nuevo item de factura
     */

    public function store(Request $request, Invoice $invoice)
    {
        $this->authorize('Update', $invoice);

        $validated = $request->validate([
            'item_id' => 'required|integer',
            'description' => 'required|string',
            'quantity' => 'required|numeric|min:0.01',
            'unit_price' => 'required|numeric|min:0',
            'tax_percent' => 'required|numeric|min:0|max:100',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        if($request->filled('item_id')) {
            $item = Item::findOrFail($request->item_id);

            //verificar que el usuario sea dueño del item
            if($item->user_id !== Auth::id()) {
                return back()->with('error', 'No tienes permiso para usar este item');
            }

            //Auto-rellenar datos del item si no se proporcionaron
            if(!$request->filled('description')) {
                $validated['description'] = $item->description ?? $item->name;
            }

            if(!$request->filled('unit_price')) {
                $validated['unit_price'] = $item->price;
            }

            if(!$request->filled('tax_percent')) {
                $validated['tax_percent'] = $item->tax_percent;
            }
        }


        //establecer valored predeterminados
        $validated['tax_percent'] = $validated['tax_percent'] ?? 0;
        $validated['discount_percent'] = $validated['discount_percent'] ?? 0;

        //calcular totales
        $subtotal = $validated['quantity'] * $validated['unit_price'];
        $tax_amount = $subtotal * ($validated['tax_percent'] / 100);
        $discount_amount = $subtotal * ($validated['discount_percent'] / 100);
        $total = $subtotal - $discount_amount + $tax_amount;

        //añadir los totales calculados
        $validated['subtotal'] = $subtotal;
        $validated['tax_amount'] = $tax_amount;
        $validated['discount_amount'] = $discount_amount;
        $validated['total'] = $total;

        //crear el item de factura
        $invoice->items()->create($validated);

        //actualizar el total de la factura

        $invoice->updateTotals();

        return redirect()->route('invoices.edit', $invoice)
            ->with('success','Concepto añadido correctamente a la factura');
    }

    /*
     * Actualizar un item de factura existente
     */
    public function update(Request $request, Invoice $invoice, InvoiceItem $invoiceItem)
    {
        $this->authorize('update', $invoice);

        //validar la solicitud
        $validated = $request->validate([
            'description' => 'required|string',
            'quantity' => 'required|numeric|min:0.01',
            'unit_price' => 'required|numeric|min:0',
            'tax_percent' => 'required|numeric|min:0|max:100',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        //calcular totales
        $subtotal = $validated['quantity'] * $validated['unit_price'];
        $tax_amount = $subtotal * ($validated['tax_percent'] / 100);
        $discount_amount = $subtotal * ($validated['discount_percent'] / 100);
        $total = $subtotal - $discount_amount + $tax_amount;

        //añadir los totales calculados
        $validated['subtotal'] = $subtotal;
        $validated['tax_amount'] = $tax_amount;
        $validated['discount_amount'] = $discount_amount;
        $validated['total'] = $total;

        //actualizar el item
        $invoiceItem->update($validated);

        //actualizar el total de la factura
        $invoice->updateTotals();

        return redirect()->route('invoices.edit', $invoice)
            ->with('success','Concepto actualizado correctamente');
    }

    /*
     * Eliminar un item de factura
     */

    public function destroy(Invoice $invoice, InvoiceItem $item)
    {
        $this->authorize('update', $invoice);

        $item->delete();

        //actalizar el total de la factura
        $invoice->updateTotals();

        return redirect()->route('invoices.edit', $invoice)
            ->with('success','Concepto eliminado correctamente');
    }
}
