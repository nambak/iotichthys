<?php

namespace App\Livewire\Device;

use App\Http\Requests\CreateDeviceRequest;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\Organization;
use Livewire\Attributes\On;
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

        return view('livewire.device.create-modal', compact('deviceModels', 'organizations', 'manufacturers'));
    }

    /**
     * 제조사 필터가 변경되면 선택된 모델을 초기화
     */
    public function updatedManufacturerFilter()
    {
        $this->device_model_id = '';
    }

    /**
     * 장치 생성
     *
     * @return void
     */
    public function save()
    {
        $request = new CreateDeviceRequest;
        $validatedData = $this->validate($request->rules(), $request->messages());

        Device::create($validatedData);

        $this->dispatch('device-created');
        $this->dispatch('modal-close', modal: 'create-device');
        $this->resetForm();
    }

    /**
     * 폼 초기화
     *
     * @return void
     */
    #[On('modal-closed')]
    public function resetForm()
    {
        $this->resetValidation();
        $this->reset();
    }
}
