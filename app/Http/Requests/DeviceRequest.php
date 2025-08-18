<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeviceRequest extends FormRequest
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
            'name'            => ['required', 'string', 'max:255'],
            'device_id'       => ['required', 'string', 'max:255', 'unique:devices,device_id'],
            'device_model_id' => ['required', 'exists:device_models,id'],
            'status'          => ['required', 'in:active,inactive,maintenance,error'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'description'     => ['nullable', 'string'],
            'location'        => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'name.required'            => __('validation.device.name.required'),
            'device_id.required'       => __('validation.device.device_id.required'),
            'device_id.unique'         => __('validation.device.device_id.unique'),
            'device_model_id.required' => __('validation.device.model_id.required'),
            'device_model_id.exists'   => __('validation.device.model_id.exists'),
            'status.required'          => __('validation.device.status.required'),
            'organization_id.exists'   => __('validation.device.organization_id.exists'),
        ];
    }
}
