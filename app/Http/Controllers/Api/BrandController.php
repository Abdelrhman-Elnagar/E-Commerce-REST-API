<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Brand\StoreBrandRequest;
use App\Http\Requests\Brand\UpdateBrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;

class BrandController
{
    public function index()
    {
        $brands = Brand::query()
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => BrandResource::collection($brands),
        ]);
    }

    public function store(StoreBrandRequest $request)
    {
        $brand = Brand::create(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Brand created successfully.',
            'data' => [
                'brand' => new BrandResource($brand),
            ],
        ], 201);
    }

    public function show(Brand $brand)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'brand' => new BrandResource($brand),
            ],
        ]);
    }

    public function update(
        UpdateBrandRequest $request,
        Brand $brand
    ) {
        $brand->update(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Brand updated successfully.',
            'data' => [
                'brand' => new BrandResource(
                    $brand->fresh()
                ),
            ],
        ]);
    }

    public function destroy(Brand $brand)
    {
        if ($brand->products()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete a brand that has products.',
            ], 409);
        }

        $brand->delete();

        return response()->json([
            'success' => true,
            'message' => 'Brand deleted successfully.',
        ]);
    }
}
