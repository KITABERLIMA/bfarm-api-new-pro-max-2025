<?php

namespace App\Http\Controllers;

use App\Http\Requests\InventoryRequest;
use App\Models\inventory;

class InventoryController extends Controller
{
    /**
     * Display a listing of the inventory.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $inventories = inventory::with('product')->get();
        return response()->json([
            'success' => true,
            'data' => $inventories,
        ]);
    }

    /**
     * Store a newly created inventory in storage.
     *
     * @param  \App\Http\Requests\InventoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InventoryRequest $request)
    {
        try {
            $inventory = Inventory::create($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Inventory created successfully.',
                'data' => $inventory,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create inventory.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified inventory.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function show(Inventory $inventory)
    {
        $inventory->load('product'); // Load related product
        return response()->json([
            'success' => true,
            'data' => $inventory,
        ]);
    }

    /**
     * Update the specified inventory in storage.
     *
     * @param  \App\Http\Requests\InventoryRequest  $request
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function update(InventoryRequest $request, Inventory $inventory)
    {
        try {
            $inventory->update($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Inventory updated successfully.',
                'data' => $inventory,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update inventory.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified inventory from storage.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inventory $inventory)
    {
        try {
            $inventory->delete();
            return response()->json([
                'success' => true,
                'message' => 'Inventory deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete inventory.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
