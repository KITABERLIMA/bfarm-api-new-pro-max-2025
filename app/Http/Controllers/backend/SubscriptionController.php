<?php

namespace App\Http\Controllers\backend;

use App\Http\Requests\SubscriptionRequest;
use App\Models\subscription;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $subscriptions = Subscription::all();

            // Format price untuk setiap subscription
            $formattedSubscriptions = $subscriptions->map(function ($subscription) {
                $subscription->price_formatted = 'Rp' . number_format($subscription->price, 2, ',', '.');
                return $subscription;
            });

            return response()->json([
                'success' => true,
                'data' => $formattedSubscriptions,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to retrieve subscriptions'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  SubscriptionRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SubscriptionRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $subscription = Subscription::create($validated);

            $formattedPrice = 'Rp' . number_format($subscription->price, 2, ',', '.');

            $subscriptionArray = $subscription->toArray();
            $subscriptionArray['price_formatted'] = $formattedPrice;

            return response()->json([
                'success' => true,
                'message' => 'Subscription created successfully.',
                'data' => $subscriptionArray,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create subscription'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  Subscription  $subscription
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Subscription $subscription): JsonResponse
    {
        try {
            // Format price ke dalam format rupiah Indonesia
            $subscription->price_formatted = 'Rp' . number_format($subscription->price, 2, ',', '.');

            return response()->json([
                'success' => true,
                'data' => $subscription,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to retrieve subscription'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  SubscriptionRequest  $request
     * @param  Subscription  $subscription
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SubscriptionRequest $request, Subscription $subscription): JsonResponse
    {
        try {
            $subscription->update($request->validated());

            // Format price ke dalam format rupiah Indonesia
            $subscription->price_formatted = 'Rp' . number_format($subscription->price, 2, ',', '.');

            return response()->json([
                'success' => true,
                'message' => 'Subscription updated successfully.',
                'data' => $subscription,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update subscription'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Subscription  $subscription
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Subscription $subscription): JsonResponse
    {
        try {
            $subscription->delete();

            return response()->json([
                'success' => true,
                'message' => 'Subscription deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete subscription'], 500);
        }
    }
}
