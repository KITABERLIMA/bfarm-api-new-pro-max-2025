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
}