<?php

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $product = $this->route('product');
        return [
            'category_id' => [
                'sometimes',
                'integer',
                'exists:categories,id',
            ],

            'brand_id' => [
                'sometimes',
                'integer',
                'exists:brands,id',
            ],

            'name' => [
                'sometimes',
                'string',
                'min:2',
                'max:200',
            ],

            'slug' => [
                'sometimes',
                'string',
                'max:220',
                Rule::unique('products', 'slug')
                    ->ignore($product->id),
            ],

            'short_description' => [
                'nullable',
                'string',
                'max:500',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'status' => [
                'sometimes',
                Rule::in([
                    'draft',
                    'active',
                    'archived',
                ]),
            ],
        ];
    }
}
