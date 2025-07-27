<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'name'                     => ['required', 'min:2', 'max:30'],
            'owner'                    => ['required', 'min:2'],
            'address'                  => ['required', 'min:10'],
            'detail_address'           => ['nullable', 'max:255'],
            'postcode'                 => ['nullable', 'numeric', 'digits:5'],
            'phone_number'             => ['required', 'numeric', 'starts_with:0', 'digits_between:9,10'],
            'business_register_number' => [
                'required',
                'numeric',
                'digits:10',
                Rule::unique('organizations', 'business_register_number')
                    ->whereNull('deleted_at')],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required'                     => __('validation.organization.name.required'),
            'name.min'                          => __('validation.organization.name.min'),
            'name.max'                          => __('validation.organization.name.max'),
            'owner.required'                    => __('validation.organization.owner.required'),
            'owner.min'                         => __('validation.organization.owner.min'),
            'address.required'                  => __('validation.organization.address.required'),
            'address.min'                       => __('validation.organization.address.min'),
            'phone_number.required'             => __('validation.organization.phoneNumber.required'),
            'phone_number.numeric'              => __('validation.organization.phoneNumber.numeric'),
            'phone_number.starts_with'          => __('validation.organization.phoneNumber.starts_with'),
            'phone_number.digits_between'       => __('validation.organization.phoneNumber.digits_between'),
            'business_register_number.required' => __('validation.organization.businessRegisterNumber.required'),
            'business_register_number.numeric'  => __('validation.organization.businessRegisterNumber.numeric'),
            'business_register_number.digits'   => __('validation.organization.businessRegisterNumber.digits'),
            'business_register_number.unique'   => __('validation.organization.businessRegisterNumber.unique'),
            'postcode.digits'                   => __('validation.organization.postcode.digits'),
            'postcode.numeric'                  => __('validation.organization.postcode.numeric'),
            'detail_address.max'                => __('validation.organization.detailAddress.max'),

        ];
    }
}
