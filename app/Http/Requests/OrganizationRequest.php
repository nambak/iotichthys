<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganizationRequest extends FormRequest
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
            'name'                   => ['required', 'min:2', 'max:30'],
            'owner'                  => ['required', 'min:2'],
            'address'                => ['required', 'min:10'],
            'phoneNumber'            => ['required', 'numeric', 'starts_with:0', 'digits_between:9,10'],
            'businessRegisterNumber' => [
                'required', 'numeric', 'digits:10', 'unique:organizations,business_register_number'
            ],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required'                   => __('validation.organization.name.required'),
            'name.min'                        => __('validation.organization.name.min'),
            'name.max'                        => __('validation.organization.name.max'),
            'owner.required'                  => __('validation.organization.owner.required'),
            'owner.min'                       => __('validation.organization.owner.min'),
            'address.required'                => __('validation.organization.address.required'),
            'address.min'                     => __('validation.organization.address.min'),
            'phoneNumber.required'            => __('validation.organization.phoneNumber.required'),
            'phoneNumber.numeric'             => __('validation.organization.phoneNumber.numeric'),
            'phoneNumber.starts_with'         => __('validation.organization.phoneNumber.starts_with'),
            'phoneNumber.digits_between'      => __('validation.organization.phoneNumber.digits_between'),
            'businessRegisterNumber.required' => __('validation.organization.businessRegisterNumber.required'),
            'businessRegisterNumber.numeric'  => __('validation.organization.businessRegisterNumber.numeric'),
            'businessRegisterNumber.digits'   => __('validation.organization.businessRegisterNumber.digits'),
            'businessRegisterNumber.unique'   => __('validation.organization.businessRegisterNumber.unique'),
        ];
    }
}
