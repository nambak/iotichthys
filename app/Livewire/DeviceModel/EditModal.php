<?php

namespace App\Livewire\DeviceModel;

use App\Http\Requests\DeviceModelRequest;
use App\Models\DeviceModel;
use Livewire\Attributes\On;
use Livewire\Component;

class EditModal extends Component
{
    public ?DeviceModel $deviceModel = null;

    public string $name = '';

    public string $manufacturer = '';

    public string $specifications = '';

    public string $description = '';

    public function render()
    {
        return view('livewire.device-model.edit-modal');
    }

    /**
     * 편집 모달 열기
     */
    #[On('open-edit-device-model')]
    public function openModal($deviceModelId)
    {
        $this->deviceModel = DeviceModel::findOrFail($deviceModelId);
        $this->loadDeviceModelData();
        $this->resetValidation();
        $this->modal('edit-device-model')->show();
    }

    /**
     * 장치 모델 데이터 로드
     */
    private function loadDeviceModelData()
    {
        if ($this->deviceModel) {
            $this->name = $this->deviceModel->name;
            $this->manufacturer = $this->deviceModel->manufacturer ?? '';
            $this->description = $this->deviceModel->description ?? '';

            // specifications를 문자열로 변환
            if ($this->deviceModel->specifications && is_array($this->deviceModel->specifications)) {
                $this->specifications = implode("\n", $this->deviceModel->specifications);
            } else {
                $this->specifications = '';
            }
        }
    }

    /**
     * 장치 모델 수정
     */
    public function save()
    {
        $request = new DeviceModelRequest;
        $validatedData = $this->validate($request->rules(), $request->messages());

        // specifications를 JSON으로 변환
        if (! empty($validatedData['specifications'])) {
            $specs = array_map('trim', explode("\n", $validatedData['specifications']));
            $specs = array_filter($specs); // 빈 줄 제거
            $validatedData['specifications'] = $specs;
        } else {
            $validatedData['specifications'] = null;
        }

        $this->deviceModel->update($validatedData);

        $this->dispatch('device-model-updated');

        $this->dispatch('modal-close', modal: 'edit-device-model');

        $this->reset();
        $this->resetValidation();
    }
}
