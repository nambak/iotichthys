<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeamRequest extends FormRequest
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
            'name' => ['required', 'min:2', 'max:50'],
            'slug' => [
                'required',
                'min:2',
                'max:50',
                'alpha_dash',
                Rule::unique('teams', 'slug')->ignore($this->route('team') ?? request('team_id')),
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
            'name.required' => __('validation.team.name.required'),
            'name.min' => __('validation.team.name.min'),
            'name.max' => __('validation.team.name.max'),
            'slug.required' => __('validation.team.slug.required'),
            'slug.min' => __('validation.team.slug.min'),
            'slug.max' => __('validation.team.slug.max'),
            'slug.alpha_dash' => __('validation.team.slug.alpha_dash'),
            'slug.unique' => __('validation.team.slug.unique'),
            'description.max' => __('validation.team.description.max'),
        ];
    }
}
