<?php

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
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
        return [
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],

            'brand_id' => [
                'required',
                'integer',
                'exists:brands,id',
            ],

            'name' => [
                'required',
                'string',
                'min:2',
                'max:200',
            ],

            'slug' => [
                'required',
                'string',
                'max:220',
                'unique:products,slug',
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
