<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {

    $availableQuantity =
            $this->quantity - $this->reserved_quantity;

        return [
            'id' => $this->id,

            'quantity' => $this->quantity,

            'reserved_quantity' =>
                $this->reserved_quantity,

            'available_quantity' =>
                $availableQuantity,

            'low_stock_threshold' =>
                $this->low_stock_threshold,

            'is_low_stock' =>
                $availableQuantity <= $this->low_stock_threshold,

            'updated_at' =>
                $this->updated_at,
        ];
    }
}
