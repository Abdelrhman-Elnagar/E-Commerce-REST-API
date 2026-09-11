<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Inventory\UpdateInventoryRequest;
use App\Http\Resources\InventoryResource;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController
{
     public function show(ProductVariant $variant)
    {
        $inventory = $variant->inventory;

        if (! $inventory) {
            return response()->json([
                'success' => false,
                'message' => 'Inventory not found for this variant.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'inventory' => new InventoryResource($inventory),
            ],
        ]);
    }

    public function update(
        UpdateInventoryRequest $request,
        ProductVariant $variant
    ) {
        $inventory = $variant->inventory;

        if (! $inventory) {
            return response()->json([
                'success' => false,
                'message' => 'Inventory not found for this variant.',
            ], 404);
        }

        $data = $request->validated();

        $quantity = $data['quantity']
            ?? $inventory->quantity;

        $reservedQuantity = $data['reserved_quantity']
            ?? $inventory->reserved_quantity;

        if ($reservedQuantity > $quantity) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Reserved quantity cannot exceed total quantity.',
            ], 422);
        }

        DB::transaction(function () use (
            $inventory,
            $data
        ) {
            $inventory->update($data);
        });

        return response()->json([
            'success' => true,
            'message' => 'Inventory updated successfully.',
            'data' => [
                'inventory' => new InventoryResource(
                    $inventory->fresh()
                ),
            ],
        ]);
    }
}
