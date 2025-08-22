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

    public string $manufacturerFilter = '';

    public function render()
    {
        $deviceModels = DeviceModel::query()
            ->when($this->manufacturerFilter, function ($query) {
                $query->where('manufacturer', $this->manufacturerFilter);
            })
            ->get();

        $organizations = Organization::all();

        // 제조사 목록을 가져오기 (중복 제거)
        $manufacturers = DeviceModel::whereNotNull('manufacturer')
            ->where('manufacturer', '!=', '')
            ->distinct()
            ->pluck('manufacturer')
            ->sort()
            ->values();

        return view('livewire.device.edit-modal', compact('deviceModels', 'organizations', 'manufacturers'));
    }

    /**
     * 제조사 필터가 변경되면 선택된 모델을 초기화 (현재 모델이 필터에 맞지 않을 경우)
     */
    public function updatedManufacturerFilter()
    {
        // 현재 선택된 모델이 새로운 필터에 맞지 않으면 초기화
        if ($this->device_model_id && $this->manufacturerFilter) {
            $selectedModel = DeviceModel::find($this->device_model_id);
            if ($selectedModel && $selectedModel->manufacturer !== $this->manufacturerFilter) {
                $this->device_model_id = '';
            }
        }
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
            $this->device_model_id = (string) $this->device->device_model_id;
            $this->status = $this->device->status;
            $this->organization_id = (string) ($this->device->organization_id ?? '');
            $this->description = $this->device->description ?? '';
            $this->location = $this->device->location ?? '';

            // 현재 모델의 제조사로 필터 설정
            if ($this->device->deviceModel && $this->device->deviceModel->manufacturer) {
                $this->manufacturerFilter = $this->device->deviceModel->manufacturer;
            }
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
