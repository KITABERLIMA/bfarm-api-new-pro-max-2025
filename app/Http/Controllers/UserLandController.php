<?php

namespace App\Http\Controllers;

use App\Models\Land;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UpdateLandRequest;
use App\Models\Address;
use Illuminate\Support\Facades\DB;

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

  public function update(UpdateLandRequest $request, Land $land): JsonResponse
  {
    // Mengambil user_id dari pengguna yang saat ini login
    $userId = auth()->user()->id;

    // Memastikan user yang login adalah pemilik tanah
    if ($land->user_id !== $userId) {
      return response()->json([
        'success' => false,
        'message' => 'You do not have permission to edit this land',
      ], 403);
    }

    $validated = $request->validated();
    DB::beginTransaction();

    try {
      // Memperbarui alamat jika ada data baru yang disediakan
      $address = Address::find($land->address_id);
      if ($address) {
        $addressData = array_filter([
          'full_address' => $validated['full_address'] ?? null,
          'village' => $validated['village'] ?? null,
          'sub_district' => $validated['sub_district'] ?? null,
          'city_district' => $validated['city_district'] ?? null,
          'province' => $validated['province'] ?? null,
          'postal_code' => $validated['postal_code'] ?? null,
        ]);
        if (!empty($addressData)) {
          $address->update($addressData);
        }
      } else {
        return response()->json([
          'success' => false,
          'message' => 'Address not found',
        ], 404);
      }


      // Memperbarui lahan jika ada data baru yang disediakan
      $landData = array_filter([
        'land_status' => $validated['land_status'] ?? null,
        'land_description' => $validated['land_description'] ?? null,
        'ownership_status' => $validated['ownership_status'] ?? null,
        'location' => $validated['location'] ?? null,
        'land_area' => $validated['land_area'] ?? null,
      ]);
      if (!empty($landData)) {
        $land->update($landData);
      }

      DB::commit();

      return response()->json([
        'success' => true,
        'message' => 'Land successfully updated',
        'data' => $land->load('user', 'address', 'landImages', 'mappedLand', 'mappedLand.landContent')
      ]);
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json([
        'success' => false,
        'message' => $e->getMessage()
      ], 500);
    }
  }

  public function destroy(Land $land): JsonResponse
  {
    // Mengambil user_id dari pengguna yang saat ini login
    $userId = auth()->user()->id;

    // Memastikan user yang login adalah pemilik tanah
    if ($land->user_id !== $userId) {
      return response()->json([
        'success' => false,
        'message' => 'You do not have permission to delete this land',
      ], 403);
    }

    if ($land->delete()) {
      return response()->json([
        'success' => true,
        'message' => 'Land successfully deleted',
      ]);
    } else {
      return response()->json([
        'success' => false,
        'message' => 'Failed to delete land',
      ], 500);
    }
  }
}