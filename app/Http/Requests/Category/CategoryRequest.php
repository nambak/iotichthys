<?php

namespace App\Http\Requests\Category;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'sort_order' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.category.name.required'),
            'name.string' => __('validation.category.name.string'),
            'name.max' => __('validation.category.name.max'),
            'description.string' => __('validation.category.description.string'),
            'description.max' => __('validation.category.description.max'),
            'parent_id.exists' => __('validation.category.parent_id.exists'),
            'sort_order.integer' => __('validation.category.sort_order.integer'),
            'sort_order.min' => __('validation.category.sort_order.min'),
            'is_active.boolean' => __('validation.category.is_active.boolean'),
        ];
    }
}