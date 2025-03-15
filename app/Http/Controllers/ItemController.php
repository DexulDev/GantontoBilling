<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * Store a newly created resource in storage.
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
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        $this->authorize('view', $item);

        return view('ítems.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        $this->authorize('update', $item);

        return view('items.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        $this->authorize('delete', $item);

        $item->delete();

        return redirect()->route('items.index')
            ->with('success','Item eliminado correctamente');
    }
}
