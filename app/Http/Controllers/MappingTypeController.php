<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMappingTypeRequest;
use App\Models\mapping_type;
use App\Models\product;
use App\Models\Product_use;
use Illuminate\Http\Request;

class MappingTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mappingTypes = mapping_type::with('productUses')->get();

        if ($mappingTypes->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Data Not Found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $mappingTypes,
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'products' => 'required|array',
            'products.*' => 'exists:products,id', // Pastikan product_id ada di tabel products
        ]);

        $mappingType = mapping_type::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        // Menyimpan product uses
        foreach ($validated['products'] as $productId) {
            Product_use::create([
                'mapping_type_id' =>  $mappingType->id,
                'product_id' => $productId,
            ]);
        }

        // Opsional: Muat ulang $mappingType dari database untuk mendapatkan relasi productUses yang baru ditambahkan
        $mappingType = $mappingType->load('productUses');

        return response()->json($mappingType, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(mapping_type $mappingType)
    {
        if (!$mappingType) {
            return response()->json([
                'success' => false,
                'message' => 'Data Not Found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $mappingType,
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, mapping_type $mappingType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $mappingType->update($request->all());

        return response()->json($mappingType);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(mapping_type $mappingType)
    {
        $mappingType->delete();

        return response()->json(null, 204);
    }
}
