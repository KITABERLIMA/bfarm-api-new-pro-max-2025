<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLandRequest;
use App\Models\Address;
use App\Models\Land;
use Illuminate\Http\JsonResponse;

class LandController extends Controller
{
    public function store(StoreLandRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Mengambil user_id dari pengguna yang saat ini login
        $userId = auth()->user()->id;

        // Membuat alamat terlebih dahulu dari data yang divalidasi
        $addressData = [
            'full_address' => $validated['full_address'],
            'village' => $validated['village'],
            'sub_district' => $validated['sub_district'],
            'city_district' => $validated['city_district'],
            'province' => $validated['province'],
            'postal_code' => $validated['postal_code'],
        ];
        $address = Address::create($addressData);

        // Setelah alamat berhasil dibuat, buat lahan dengan mengaitkan address_id dan user_id dari pengguna yang login
        $landData = [
            'user_id' => $userId,
            'address_id' => $address->id,
            'land_status' => $validated['land_status'] ?? null, // Menggunakan null coalescing operator jika field bersifat nullable
            'land_description' => $validated['land_description'] ?? null,
            'ownership_status' => $validated['ownership_status'] ?? null,
            'location' => $validated['location'],
            'land_area' => $validated['land_area'],
        ];
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

    //make get function for get land data with user, address and land image
    public function show($id): JsonResponse
    {
        $land = Land::with('user', 'address', 'landImages', 'mappedLand', 'mappedLand.landContent')->find($id);

        if ($land) {
            return response()->json([
                'success' => true,
                'message' => 'Land found',
                'data' => $land
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Land not found',
            ], 404);
        }
    }
}
