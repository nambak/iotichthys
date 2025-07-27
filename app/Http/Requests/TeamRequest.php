<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'organization_id' => ['required', 'exists:organizations,id'],
            'name' => ['required', 'min:2', 'max:50'],
            'slug' => [
                'required',
                'min:2',
                'max:50',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('teams', 'slug')
                    ->whereNull('deleted_at')
            ],
            'description' => ['nullable', 'max:500'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'organization_id.required' => __('validation.team.organization_id.required'),
            'organization_id.exists' => __('validation.team.organization_id.exists'),
            'name.required' => __('validation.team.name.required'),
            'name.min' => __('validation.team.name.min'),
            'name.max' => __('validation.team.name.max'),
            'slug.required' => __('validation.team.slug.required'),
            'slug.min' => __('validation.team.slug.min'),
            'slug.max' => __('validation.team.slug.max'),
            'slug.regex' => __('validation.team.slug.regex'),
            'slug.unique' => __('validation.team.slug.unique'),
            'description.max' => __('validation.team.description.max'),
        ];
    }
}