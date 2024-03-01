<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLandRequest;
use App\Models\Address;
use App\Models\Land;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LandController extends Controller
{
    public function store(StoreLandRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Membuat alamat terlebih dahulu
        $address = Address::create($validated['address']);

        // Setelah alamat berhasil dibuat, buat lahan dengan mengaitkan address_id
        $landData = $validated['land'] + ['address_id' => $address->id];
        $land = Land::create($landData);

        if ($land) {
            return response()->json([
                'success' => true,
                'message' => 'Land successfully created',
                'data' => $land
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create land',
            ], 500);
        }
    }
}
