<?php

namespace App\Livewire\Device;

use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\Organization;
use Livewire\Component;

class CreateModal extends Component
{
    public string $name = '';
    public string $device_id = '';
    public string $device_model_id = '';
    public string $status = 'active';
    public string $organization_id = '';
    public string $description = '';
    public string $location = '';

    public function render()
    {
        $deviceModels = DeviceModel::all();
        $organizations = Organization::all();

        return view('livewire.device.create-modal', compact('deviceModels', 'organizations'));
    }

    /**
     * 장치 생성
     *
     * @return void
     */
    public function save()
    {
        $validatedData = $this->validate([
            'name' => 'required|string|max:255',
            'device_id' => 'required|string|max:255|unique:devices,device_id',
            'device_model_id' => 'required|exists:device_models,id',
            'status' => 'required|in:active,inactive,maintenance,error',
            'organization_id' => 'nullable|exists:organizations,id',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
        ], [
            'name.required' => '장치명을 입력해주세요.',
            'device_id.required' => '장치 ID를 입력해주세요.',
            'device_id.unique' => '이미 존재하는 장치 ID입니다.',
            'device_model_id.required' => '장치 모델을 선택해주세요.',
            'device_model_id.exists' => '유효하지 않은 장치 모델입니다.',
            'status.required' => '장치 상태를 선택해주세요.',
            'organization_id.exists' => '유효하지 않은 조직입니다.',
        ]);

        Device::create($validatedData);

        $this->modal('create-device')->close();

        $this->dispatch('device-created');

        $this->resetForm();
    }

    /**
     * 폼 초기화
     *
     * @return void
     */
    public function resetForm()
    {
        $this->name = '';
        $this->device_id = '';
        $this->device_model_id = '';
        $this->status = 'active';
        $this->organization_id = '';
        $this->description = '';
        $this->location = '';
    }
}
