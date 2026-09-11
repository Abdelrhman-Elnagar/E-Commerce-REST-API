<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController
{
     public function index(Request $request)
    {
        // $perPage = min($request->integer('per_page', 15), 100);
        $products = Product::query()
            ->with(['category', 'brand'])
            ->latest()
            ->paginate(
                $request->integer('per_page', 15)
            );

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products),
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $product = Product::create(
            $request->validated()
        );

        $product->load([
            'category',
            'brand',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
            'data' => [
                'product' => new ProductResource($product),
            ],
        ], 201);
    }

    public function show(Product $product)
    {
        $product->load([
            'category',
            'brand',
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'product' => new ProductResource($product),
            ],
        ]);
    }

    public function update(
        UpdateProductRequest $request,
        Product $product
    ) {
        $product->update(
            $request->validated()
        );

        $product->load([
            'category',
            'brand',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'data' => [
                'product' => new ProductResource(
                    $product->fresh([
                        'category',
                        'brand',
                    ])
                ),
            ],
        ]);
    }

    public function destroy(Product $product)
    {
        if ($product->variants()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete a product that has variants.',
            ], 409);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
        ]);
    }
}
