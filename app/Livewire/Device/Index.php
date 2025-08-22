<?php

namespace App\Livewire\Device;

use App\Models\Device;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        $devices = Device::with(['deviceModel', 'organization'])
            ->withCount('configs')
            ->paginate(10);

        return view('livewire.device.index', compact('devices'));
    }

    /**
     * 장치 삭제
     *
     * @param  int  $deviceId
     */
    public function delete($deviceId): void
    {
        $device = Device::findOrFail($deviceId);

        $device->delete();

        $this->dispatch('device-deleted');
        $this->resetPage();
    }

    /**
     * 장치 편집 모달 열기
     *
     * @param  int  $deviceId
     */
    public function editDevice(Device $device): void
    {
        $this->dispatch('open-edit-device', deviceId: $device->id);
    }

    /**
     * 장치 생성 성공 시 처리
     */
    #[On('device-created')]
    public function refreshAfterCreate()
    {
        $this->resetPage();
    }

    /**
     * 장치 수정 성공 시 처리
     */
    #[On('device-updated')]
    public function refreshAfterUpdate()
    {
        $this->resetPage();
    }
}
