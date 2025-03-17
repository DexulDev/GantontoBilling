<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    /**
     * Mostrar una lista paginada de items.
     */
    public function index()
    {
        $items = Item::where('user_id', Auth::id())
            ->orderBy('name')
            ->paginate(10);

        return view('items.index',
            compact('items'));
    }

    /**
     * Mostrar el formulario para crear un nuevo item.
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * Crear un nuevo item en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'tax_percent' => 'required|numeric|min:0|max:100',
            'category' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255|unique:items',
            'stock' => 'nullable|integer|min:0',
            'type' => 'required|string|in:product,service,subscription',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['tax_percent'] = $validated['tax_percent'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        Item::create($validated);

        return redirect()->route('items.index')
            ->with('success','Item creado correctamente');
    }

    /**
     * Mostrar el item específico.
     */
    public function show(Item $item)
    {
        $this->authorize('view', $item);

        return view('ítems.show', compact('item'));
    }

    /**
     * Mostrar el formulario para editar un item.
     */
    public function edit(Item $item)
    {
        $this->authorize('update', $item);

        return view('items.edit', compact('item'));
    }

    /**
     * Actualizar un item existente.
     */
    public function update(Request $request, Item $item)
    {
        $this->authorize('update', $item);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'tax_percent' => 'required|numeric|min:0|max:100',
            'category' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:100|unique:items,sku,'.$item->id,
            'stock' => 'nullable|integer|min:0',
            'type' => 'required|string|in:product,service,subscription',
        ]);

        $validated['tax_percent'] = $validated['tax_percent'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        $item->update($validated);

        return redirect()->route('items.index')
            ->with('success','Item actualizado correctamente');
    }

    /**
     * Eliminar un item.
     */
    public function destroy(Item $item)
    {
        $this->authorize('delete', $item);

        $item->delete();

        return redirect()->route('items.index')
            ->with('success','Item eliminado correctamente');
    }
}
