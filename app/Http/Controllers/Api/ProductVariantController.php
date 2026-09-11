<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProductVariant\StoreProductVariantRequest;
use App\Http\Requests\ProductVariant\UpdateProductVariantRequest;
use App\Http\Resources\ProductVariantResource;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductVariantController
{
     public function index(Product $product)
    {
        $variants = $product->variants()
            ->with('inventory')
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => ProductVariantResource::collection($variants),
        ]);
    }

    public function store(
        StoreProductVariantRequest $request,
        Product $product
    ) {
        // $variant = $product->variants()->create(
        //     $request->validated()
        // );

        $variant = DB::transaction(function () use (
    $product,
    $request
) {
    $variant = $product->variants()->create(
        $request->validated()
    );

    $variant->inventory()->create([
        'quantity' => 0,
        'reserved_quantity' => 0,
        'low_stock_threshold' => 5,
    ]);

    return $variant;
});

        $variant->load('product');

        return response()->json([
            'success' => true,
            'message' => 'Product variant created successfully.',
            'data' => [
                'variant' => new ProductVariantResource($variant),
            ],
        ], 201);
    }

    public function show(Product $product, ProductVariant $variant)
    {
        $this->ensureVariantBelongsToProduct($product, $variant);

        $variant->load([
            'product',
            'inventory',
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'variant' => new ProductVariantResource($variant),
            ],
        ]);
    }

    public function update(
        UpdateProductVariantRequest $request,
        Product $product,
        ProductVariant $variant
    ) {
        $this->ensureVariantBelongsToProduct($product, $variant);

        $variant->update(
            $request->validated()
        );

        $variant->load([
            'product',
            'inventory',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product variant updated successfully.',
            'data' => [
                'variant' => new ProductVariantResource(
                    $variant->fresh([
                        'product',
                        'inventory',
                    ])
                ),
            ],
        ]);
    }

    public function destroy(
        Product $product,
        ProductVariant $variant
    ) {
        $this->ensureVariantBelongsToProduct($product, $variant);

        if ($product->variants()->count() === 1) {
            return response()->json([
                'success' => false,
                'message' => 'A product must have at least one variant.',
            ], 409);
        }

        $variant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product variant deleted successfully.',
        ]);
    }

    private function ensureVariantBelongsToProduct(
        Product $product,
        ProductVariant $variant
    ): void {
        abort_unless(
            $variant->product_id === $product->id,
            404
        );
}
}
