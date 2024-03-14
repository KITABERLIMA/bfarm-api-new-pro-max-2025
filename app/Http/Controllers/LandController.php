<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLandRequest;
use App\Models\Address;
use App\Models\Land;
use Illuminate\Http\JsonResponse;

class LandController extends Controller
{

    public function index(): JsonResponse
    {
        $lands = Land::with('user', 'address', 'landImages', 'mappedLand', 'mappedLand.landContent')->get();

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

    public function listAll(): JsonResponse
    {
        $lands = Land::with('user', 'address', 'landImages', 'mappedLand')
            ->leftJoin('mapped_lands', 'lands.id', '=', 'mapped_lands.land_id')
            ->select('lands.*', 'mapped_lands.updated_at as terpetakan')
            ->join('users', 'lands.user_id', '=', 'users.id')
            ->leftJoin('user_individuals', 'users.id', '=', 'user_individuals.user_id')
            ->leftJoin('user_companies', 'users.id', '=', 'user_companies.user_id')
            ->selectRaw('CONCAT(COALESCE(user_individuals.first_name, user_companies.first_name), " ", COALESCE(user_individuals.last_name, user_companies.last_name)) as name')
            ->get();

        foreach ($lands as $land) {
            if ($land->mappedLand) {
                $land->mapped = true;
            } else {
                $land->mapped = false;
            }

            // Get the address data for the land
            $address = Address::find($land->address_id);
            if ($address) {
                $alamat = sprintf('%s, %s, %s, %s', $address->village, $address->sub_district, $address->city_district, $address->province);
                $land->alamat = $alamat;
            }
        }
        return response()->json([
            'success' => true,
            'message' => 'All lands retrieved',
            'data' => $lands->map(function ($land) {
                unset($land->mappedLand);
                return $land;
            })
        ]);
    }

    public function listMapped(): JsonResponse
    {
        $lands = Land::with('user', 'address', 'landImages', 'mappedLand')
            ->join('users', 'lands.user_id', '=', 'users.id')
            ->leftJoin('user_individuals', 'users.id', '=', 'user_individuals.user_id')
            ->leftJoin('user_companies', 'users.id', '=', 'user_companies.user_id')
            ->leftJoin('mapped_lands', 'lands.id', '=', 'mapped_lands.land_id')
            ->select('lands.*', 'mapped_lands.updated_at as terpetakan')
            ->selectRaw('CONCAT(COALESCE(user_individuals.first_name, user_companies.first_name), " ", COALESCE(user_individuals.last_name, user_companies.last_name)) as name')
            ->whereNotNull('mapped_lands.land_id')
            ->get();

        foreach ($lands as $land) {
            if ($land->mappedLand) {
                $land->mapped = true;
            } else {
                $land->mapped = false;
            }

            // Get the address data for the land
            $address = Address::find($land->address_id);
            if ($address) {
                $alamat = sprintf('%s, %s, %s, %s', $address->village, $address->sub_district, $address->city_district, $address->province);
                $land->alamat = $alamat;
            }
        }
        return response()->json([
            'success' => true,
            'message' => 'Mapped lands retrieved',
            'data' => $lands->map(function ($land) {
                unset($land->mappedLand);
                return $land;
            })
        ]);
    }

    public function listUnmapped(): JsonResponse
    {
        $lands = Land::with('user', 'address', 'landImages', 'mappedLand')
            ->join('users', 'lands.user_id', '=', 'users.id')
            ->leftJoin('user_individuals', 'users.id', '=', 'user_individuals.user_id')
            ->leftJoin('user_companies', 'users.id', '=', 'user_companies.user_id')
            ->leftJoin('mapped_lands', 'lands.id', '=', 'mapped_lands.land_id')
            ->select('lands.*', 'mapped_lands.updated_at as terpetakan')
            ->selectRaw('CONCAT(COALESCE(user_individuals.first_name, user_companies.first_name), " ", COALESCE(user_individuals.last_name, user_companies.last_name)) as name')
            ->whereNull('mapped_lands.land_id')
            ->get();

        foreach ($lands as $land) {
            if ($land->mappedLand) {
                $land->mapped = true;
            } else {
                $land->mapped = false;
            }

            // Get the address data for the land
            $address = Address::find($land->address_id);
            if ($address) {
                $alamat = sprintf('%s, %s, %s, %s', $address->village, $address->sub_district, $address->city_district, $address->province);
                $land->alamat = $alamat;
            }
        }
        return response()->json([
            'success' => true,
            'message' => 'Unmapped lands retrieved',
            'data' => $lands->map(function ($land) {
                unset($land->mappedLand);
                return $land;
            })
        ]);
    }
}
