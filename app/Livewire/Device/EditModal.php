<?php

namespace App\Livewire\Device;

use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\Organization;
use Livewire\Attributes\On;
use Livewire\Component;

class EditModal extends Component
{
    public ?Device $device = null;
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

        return view('livewire.device.edit-modal', compact('deviceModels', 'organizations'));
    }

    /**
     * 편집 모달 열기
     */
    #[On('open-edit-device')]
    public function openModal($deviceId)
    {
        $this->device = Device::findOrFail($deviceId);
        $this->loadDeviceData();
        $this->modal('edit-device')->show();
    }

    /**
     * 장치 데이터 로드
     */
    private function loadDeviceData()
    {
        if ($this->device) {
            $this->name = $this->device->name;
            $this->device_id = $this->device->device_id;
            $this->device_model_id = (string) $this->device->device_model_id;
            $this->status = $this->device->status;
            $this->organization_id = (string) ($this->device->organization_id ?? '');
            $this->description = $this->device->description ?? '';
            $this->location = $this->device->location ?? '';
        }
    }

    /**
     * 장치 수정
     */
    public function save()
    {
        $validatedData = $this->validate([
            'name' => 'required|string|max:255',
            'device_id' => 'required|string|max:255|unique:devices,device_id,' . $this->device->id,
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

        $this->device->update($validatedData);

        $this->modal('edit-device')->close();

        $this->dispatch('device-updated');

        $this->resetForm();
    }

    /**
     * 폼 초기화
     */
    public function resetForm()
    {
        $this->device = null;
        $this->name = '';
        $this->device_id = '';
        $this->device_model_id = '';
        $this->status = 'active';
        $this->organization_id = '';
        $this->description = '';
        $this->location = '';
    }
}
