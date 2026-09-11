<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProductImage\StoreProductImageRequest;
use App\Http\Requests\ProductImage\UpdateProductImageRequest;
use App\Http\Resources\ProductImageResource;
use App\Models\Product;
use App\Models\ProductImage;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductImageController
{
    public function index(Product $product)
    {
        $images = $product->images()
            ->orderByDesc('is_primary')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ProductImageResource::collection($images),
        ]);
    }

    //multipart/form-data -> file nit json
    public function store(
        StoreProductImageRequest $request,
        Product $product
    ) {
        $path = $request->file('image')
            ->store('products', 'public');

        $isPrimary = $request->boolean('is_primary');

        $image = DB::transaction(function () use (
            $product,
            $request,
            $path,
            $isPrimary
        ) {
            if ($isPrimary) {
                $product->images()->update([
                    'is_primary' => false,
                ]);
            }

            return $product->images()->create([
                'path' => $path,
                'is_primary' => $isPrimary,
                'sort_order' => $request->integer(
                    'sort_order',
                    0
                ),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Product image uploaded successfully.',
            'data' => [
                'image' => new ProductImageResource($image),
            ],
        ], 201);
    }

    public function update(
        UpdateProductImageRequest $request,
        Product $product,
        ProductImage $image
    ) {
        $this->ensureImageBelongsToProduct($product, $image);

        $data = $request->validated();

        DB::transaction(function () use (
            $product,
            $image,
            $data
        ) {
            if (($data['is_primary'] ?? false) === true) {
                $product->images()
                    ->where('id', '!=', $image->id)
                    ->update([
                        'is_primary' => false,
                    ]);
            }

            $image->update($data);
        });

        return response()->json([
            'success' => true,
            'message' => 'Product image updated successfully.',
            'data' => [
                'image' => new ProductImageResource(
                    $image->fresh()
                ),
            ],
        ]);
    }

    public function destroy(
        Product $product,
        ProductImage $image
    ) {
        $this->ensureImageBelongsToProduct($product, $image);

        Storage::disk('public')->delete($image->path);

        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product image deleted successfully.',
        ]);
    }

    private function ensureImageBelongsToProduct(
        Product $product,
        ProductImage $image
    ): void {
        abort_unless(
            $image->product_id === $product->id,
            404
        );
    }
}
