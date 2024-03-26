<?php

namespace App\Http\Controllers\backend;

use App\Http\Requests\StoreMappingTypeRequest;
use App\Models\mapping_type;
use App\Models\Product_use;
use App\Http\Controllers\Controller;


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


    public function store(StoreMappingTypeRequest $request)
    {
        $validated = $request->validated();

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
    public function show($id)
    {
        // Cari mappingType berdasarkan ID. Gunakan first() untuk mendapatkan single model atau null jika tidak ditemukan
        $mappingType = mapping_type::find($id);

        // Cek apakah mappingType yang akan dihapus benar-benar ada
        if (!$mappingType) {
            return response()->json([
                'success' => false,
                'message' => 'Mapping Type not found.',
            ], 404); // Not Found
        }

        if (!$mappingType) {
            return response()->json([
                'success' => false,
                'message' => 'Data Not Found.',
            ], 404);
        }

        // Muat relasi productUses ketika mengembalikan response
        $mappingType->load('productUses');

        return response()->json([
            'success' => true,
            'data' => $mappingType,
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(StoreMappingTypeRequest $request, $id)
    {
        $validated = $request->validated();

        // Cari mappingType berdasarkan ID. Gunakan first() untuk mendapatkan single model atau null jika tidak ditemukan
        $mappingType = mapping_type::find($id);

        // Cek apakah mappingType yang akan dihapus benar-benar ada
        if (!$mappingType) {
            return response()->json([
                'success' => false,
                'message' => 'Mapping Type not found.',
            ], 404); // Not Found
        }

        // Update mapping_type
        $mappingType->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        // Hapus relasi lama
        $mappingType->productUses()->delete();

        // Buat relasi baru berdasarkan produk yang diterima
        foreach ($validated['products'] as $productId) {
            Product_use::create([
                'mapping_type_id' => $mappingType->id,
                'product_id' => $productId,
            ]);
        }

        // Muat ulang mappingType untuk mendapatkan relasi terkini
        $mappingType = $mappingType->load('productUses');

        return response()->json($mappingType);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Cari mappingType berdasarkan ID. Gunakan first() untuk mendapatkan single model atau null jika tidak ditemukan
        $mappingType = mapping_type::find($id);

        // Cek apakah mappingType yang akan dihapus benar-benar ada
        if (!$mappingType) {
            return response()->json([
                'success' => false,
                'message' => 'Mapping Type not found.',
            ], 404); // Not Found
        }

        try {
            // Hapus relasi terlebih dahulu untuk menghindari constraint violation
            $mappingType->productUses()->delete();

            // Kemudian, hapus mappingType itu sendiri
            $mappingType->delete();

            return response()->json([
                'success' => true,
                'message' => 'Mapping Type deleted successfully.',
            ], 200); // OK
        } catch (\Exception $e) {
            // Tangani kemungkinan exception/error yang terjadi saat penghapusan
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete Mapping Type.',
                'error' => $e->getMessage(), // Opsional, tergantung kebutuhan
            ], 500); // Internal Server Error
        }
    }
}
