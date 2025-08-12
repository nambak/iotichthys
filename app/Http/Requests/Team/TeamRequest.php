<?php

namespace App\Http\Requests\Team;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TeamRequest extends FormRequest
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
            'organization_id' => ['required', 'exists:organizations,id'],
            'name'            => ['required', 'min:2', 'max:50'],
            'description'     => ['nullable', 'max:500'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'organization_id.required' => __('validation.team.organization_id.required'),
            'organization_id.exists'   => __('validation.team.organization_id.exists'),
            'name.required'            => __('validation.team.name.required'),
            'name.min'                 => __('validation.team.name.min'),
            'name.max'                 => __('validation.team.name.max'),
            'description.max'          => __('validation.team.description.max'),
        ];
    }
}