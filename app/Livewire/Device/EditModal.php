<?php

namespace App\Livewire\Device;

use App\Http\Requests\UpdateDeviceRequest;
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
        $this->dispatch('open-modal', 'edit-device');
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
            $this->device_model_id = (string)$this->device->device_model_id;
            $this->status = $this->device->status;
            $this->organization_id = (string)($this->device->organization_id ?? '');
            $this->description = $this->device->description ?? '';
            $this->location = $this->device->location ?? '';
        }
    }

    /**
     * 장치 수정
     */
    public function save()
    {
        $request = new UpdateDeviceRequest;
        $validatedData = $this->validate($request->rules(), $request->messages());

        $this->device->update($validatedData);

        $this->dispatch('device-updated');

        $this->dispatch('modal-close', modal: 'edit-device');

        $this->resetForm();
    }

    /**
     * 폼 초기화
     */
    public function resetForm()
    {
        $this->resetValidation();
    }
}
