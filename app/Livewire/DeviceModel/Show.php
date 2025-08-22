<?php

namespace App\Livewire\DeviceModel;

use App\Models\DeviceModel;
use Livewire\Component;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;

    public DeviceModel $deviceModel;

    public function mount(DeviceModel $deviceModel)
    {
        $this->deviceModel = $deviceModel;
    }

    public function render()
    {
        // 이 모델을 사용하는 장치들을 페이지네이션으로 가져오기
        $devices = $this->deviceModel->devices()
            ->with(['organization'])
            ->paginate(10);

        return view('livewire.device-model.show', compact('devices'));
    }

    /**
     * 뒤로 가기
     */
    public function goBack()
    {
        return redirect()->route('device-model.index');
    }
}
