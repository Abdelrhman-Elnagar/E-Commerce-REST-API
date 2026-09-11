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
    //filter products based on category_id, brand_id, status, and search query
    $products = Product::query()
        ->with(['category', 'brand'])
        ->when(
            $request->filled('category_id'),
            fn ($query) => $query->where(
                'category_id',
                $request->integer('category_id')
            )
        )
        ->when(
            $request->filled('brand_id'),
            fn ($query) => $query->where(
                'brand_id',
                $request->integer('brand_id')
            )
        )
        ->when(
            $request->filled('status'),
            fn ($query) => $query->where(
                'status',
                $request->string('status')
            )
        )
        ->when(
            $request->filled('search'),
            fn ($query) => $query->where(function ($query) use ($request) {
                $search = $request->string('search');

                $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            })
        )
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
