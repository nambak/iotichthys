<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateDeviceRequest extends DeviceRequest
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
            'name' => ['required', 'string', 'max:255'],
            'device_model_id' => ['required', 'exists:device_models,id'],
            'status' => ['required', 'in:active,inactive,maintenance,error'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'device_id' => [
                'required',
                'string',
                'max:255',
                Rule::unique('devices')
                    ->whereNull('deleted_at')
                    ->whereNot('device_id', $this->input('device_id')),
            ],
        ];
    }
}
