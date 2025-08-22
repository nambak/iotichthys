<?php

namespace App\Livewire\DeviceModel;

use App\Http\Requests\DeviceModelRequest;
use App\Models\DeviceModel;
use Livewire\Attributes\On;
use Livewire\Component;

class CreateModal extends Component
{
    public string $name = '';

    public string $manufacturer = '';

    public string $specifications = '';

    public string $description = '';

    public function render()
    {
        return view('livewire.device-model.create-modal');
    }

    /**
     * 장치 모델 생성
     *
     * @return void
     */
    public function save()
    {
        $request = new DeviceModelRequest;
        $validatedData = $this->validate($request->rules(), $request->messages());

        // specifications를 JSON으로 변환 (배열 형태가 아닌 경우)
        if (! empty($validatedData['specifications'])) {
            // 간단한 텍스트를 JSON 배열로 변환
            $specs = array_map('trim', explode("\n", $validatedData['specifications']));
            $specs = array_filter($specs); // 빈 줄 제거
            $validatedData['specifications'] = $specs;
        } else {
            $validatedData['specifications'] = null;
        }

        DeviceModel::create($validatedData);

        $this->dispatch('device-model-created');
        $this->dispatch('modal-close', modal: 'create-device-model');
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
