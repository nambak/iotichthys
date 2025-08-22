<?php

namespace App\Livewire\DeviceModel;

use App\Models\DeviceModel;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        $deviceModels = DeviceModel::withCount('devices')
            ->paginate(10);

        return view('livewire.device-model.index', compact('deviceModels'));
    }

    /**
     * 장치 모델 삭제
     *
     * @param  int  $deviceModelId
     */
    public function delete($deviceModelId): void
    {
        $deviceModel = DeviceModel::findOrFail($deviceModelId);

        // 연결된 장치가 있는지 확인
        if (! $deviceModel->canBeDeleted()) {
            $this->dispatch('show-error-toast', [
                'message' => '이 모델을 사용하는 장치가 있어 삭제할 수 없습니다.',
            ]);

            return;
        }

        $deviceModel->delete();

        $this->dispatch('device-model-deleted');
        $this->resetPage();
    }

    /**
     * 장치 모델 편집 모달 열기
     */
    public function editDeviceModel(int $deviceModelId): void
    {
        $deviceModel = DeviceModel::findOrFail($deviceModelId);
        $this->dispatch('open-edit-device-model', deviceModelId: $deviceModel->id);
    }

    /**
     * 장치 모델 생성 성공 시 처리
     */
    #[On('device-model-created')]
    public function refreshAfterCreate()
    {
        $this->resetPage();
    }

    /**
     * 장치 모델 수정 성공 시 처리
     */
    #[On('device-model-updated')]
    public function refreshAfterUpdate()
    {
        $this->resetPage();
    }
}
