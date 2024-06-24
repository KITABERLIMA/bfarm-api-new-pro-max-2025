<?php

namespace App\Http\Controllers;

use App\Models\Land;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreLandRequest;
use App\Models\Address;

class UserLandController extends Controller
{
  public function index(): JsonResponse
  {
    // Mengambil user yang sedang login
    $userId = auth()->user()->id;

    // Mengambil data tanah yang berkaitan dengan user yang sedang login
    $lands = Land::with('user', 'address', 'landImages', 'mappedLand', 'mappedLand.landContent')
      ->where('user_id', $userId)
      ->get();

    return response()->json([
      'success' => true,
      'message' => 'All lands retrieved',
      'data' => $lands
    ]);
  }


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
      'land_status' => $validated['land_status'] ?? null,
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
}