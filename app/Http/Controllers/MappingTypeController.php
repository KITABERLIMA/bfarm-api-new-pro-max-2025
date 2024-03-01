<?php

namespace App\Http\Controllers;

use App\Models\mapping_type;
use Illuminate\Http\Request;

class MappingTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mappingTypes = mapping_type::all();

        if ($mappingTypes->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Data Not Found.',
            ], 404); // Menggunakan kode status 404 Not Found
        }

        return response()->json([
            'success' => true,
            'data' => $mappingTypes,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $mappingType = mapping_type::create($request->all());

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
