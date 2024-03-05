<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $products = Product::all();

        // Format price untuk setiap product
        $formattedProducts = $products->map(function ($product) {
            $product->price_formatted = 'Rp' . number_format($product->price, 2, ',', '.');
            return $product;
        });

        return response()->json([
            'success' => true,
            'data' => $formattedProducts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProductRequest $request)
    {
        $validated = $request->validated();

        $product = Product::create($validated);

        $formattedPrice = 'Rp' . number_format($product->price, 2, ',', '.');

        $productArray = $product->toArray();
        $productArray['price_formatted'] = $formattedPrice;

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
            'data' => $productArray,
        ], 201);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Product $product)
    {
        // Format price ke dalam format rupiah Indonesia
        $product->price_formatted = 'Rp' . number_format($product->price, 2, ',', '.');

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        // Format price ke dalam format rupiah Indonesia
        $product->price_formatted = 'Rp' . number_format($product->price, 2, ',', '.');

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'data' => $product,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
        ]);
    }
}
