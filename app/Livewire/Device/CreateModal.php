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
        $request = new CreateDeviceRequest();
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
