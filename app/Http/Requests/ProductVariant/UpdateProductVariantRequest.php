<?php

namespace App\Http\Requests\ProductVariant;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductVariantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
       $variant = $this->route('variant');

        return [
            'sku' => [
                'sometimes',
                'string',
                'max:100',
                Rule::unique('product_variants', 'sku')
                    ->ignore($variant->id),
            ],

            'price' => [
                'sometimes',
                'numeric',
                'min:0',
            ],

            'discount_price' => [
                'nullable',
                'numeric',
                'min:0',
                'lte:price',
            ],

            'attributes' => [
                'nullable',
                'array',
            ],

            'status' => [
                'sometimes',
                'in:active,inactive',
            ],
        ];
    }
}
