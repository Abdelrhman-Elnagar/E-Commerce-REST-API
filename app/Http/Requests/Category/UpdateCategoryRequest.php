<?php

namespace App\Http\Requests\Category;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
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
                $category = $this->route('category');
        return [
             'parent_id' => [
                'nullable',
                'integer',
                'exists:categories,id',
                Rule::notIn([$category->id]),
            ],

            'name' => [
                'sometimes',
                'string',
                'min:2',
                'max:100',
            ],

            'slug' => [
                'sometimes',
                'string',
                'max:120',
                Rule::unique('categories', 'slug')
                    ->ignore($category->id),
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }
}
