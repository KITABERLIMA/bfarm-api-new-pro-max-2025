<?php

namespace App\Http\Controllers;

use App\Models\product;
use Illuminate\Support\Str;
use App\Http\Requests\ProductRequest;
use App\Models\product_image;
use Illuminate\Support\Facades\Storage;

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

        // Buat entri produk
        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/products', $imageName);

                // Buat entri baru di tabel product_image untuk setiap gambar
                product_image::create([
                    'product_id' => $product->id, // Asumsikan ada kolom 'product_id' di tabel 'product_images'
                    'image' => $imageName,
                ]);
            }
        }

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
        $validated = $request->validated();

        // Ambil daftar nama gambar lama
        $oldImages = $product->images()->pluck('image')->toArray();

        // Cek apakah ada file gambar yang dikirimkan
        if ($request->hasFile('images')) {
            // Hapus gambar-gambar lama dari storage
            foreach ($oldImages as $oldImage) {
                Storage::disk('public')->delete($oldImage);
            }

            // Hapus semua entri gambar lama dari tabel product_image
            $product->images()->delete();

            // Loop untuk setiap file gambar yang dikirimkan
            foreach ($request->file('images') as $image) {
                // Simpan file gambar ke dalam storage
                $imageName = time() . '.' . Str::random(32) . "." . $image->getClientOriginalExtension();
                Storage::disk('public')->put($imageName, file_get_contents($image));

                // Simpan informasi gambar ke dalam tabel product_image
                product_image::create([
                    'product_id' => $product->id,
                    'image' => $imageName,
                ]);
            }
        }

        // Lakukan update data produk
        $product->update($validated);

        // Format harga ke dalam format rupiah Indonesia
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
