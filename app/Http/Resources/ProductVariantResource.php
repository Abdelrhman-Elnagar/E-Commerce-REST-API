<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
      return [
            'id' => $this->id,
            'sku' => $this->sku,
            'price' => $this->price,
            'discount_price' => $this->discount_price,

            'attributes' => $this->attributes,

            'status' => $this->status,

            'product' => $this->whenLoaded(
                'product',
                fn () => [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
                    'slug' => $this->product->slug,
                ]
            ),

            'inventory' => $this->whenLoaded(
                'inventory',
                fn () => [
                    'quantity' => $this->inventory->quantity,
                    'reserved_quantity' => $this->inventory->reserved_quantity,
                    'low_stock_threshold' =>
                        $this->inventory->low_stock_threshold,
                ]
            ),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
