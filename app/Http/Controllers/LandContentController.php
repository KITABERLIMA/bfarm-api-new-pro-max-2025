<?php

namespace App\Http\Controllers;

use App\Http\Requests\LandContentRequest;
use App\Models\land_content_history;

class LandContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = land_content_history::select('id', 'updated_at')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data retrieved successfully.',
            'data' => $data
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(LandContentRequest $request)
    {
        try {
            $validated = $request->validated();

            $landContentHistory = land_content_history::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Land content history successfully created.',
                'data' => $landContentHistory
            ], 201); // HTTP status code 201 indicates that a resource has been successfully created.
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create land content history.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(land_content_history $land_content_history)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Data retrieved successfully.',
                'data' => $land_content_history
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Land content history not found.',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\land_content_history  $land_content_history
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(land_content_history $land_content_history) // Ubah parameter fungsi untuk menerima $id
    {
        try {
            $land_content_history->delete();

            return response()->json([
                'success' => true,
                'message' => 'Land content history successfully deleted.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Menangkap kesalahan jika model tidak ditemukan
            return response()->json([
                'success' => false,
                'message' => 'Land content history not found.',
            ], 404);
        } catch (\Exception $e) {
            // Menangkap kesalahan umum lainnya
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete land content history.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
