<?php

namespace App\Livewire\Permissions;

use App\Models\Permission;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        $permissions = Permission::with(['roles.users'])
            ->orderBy('resource', 'asc')
            ->orderBy('action', 'asc')
            ->paginate(10);

        return view('livewire.permissions.index', compact('permissions'));
    }

    /**
     * 권한 삭제
     *
     * @param int $permissionId
     * @return void
     */
    public function delete($permissionId): void
    {
        $permission = Permission::findOrFail($permissionId);

        // 권한이 사용 중인지 확인
        if ($permission->roles()->count() > 0) {
            $this->dispatch('show-error-toast', ['message' => '역할에 할당된 권한은 삭제할 수 없습니다.']);
            return;
        }

        $permission->delete();

        $this->dispatch('permission-deleted');
        $this->resetPage();
    }

    /**
     * 권한 편집 모달 열기
     *
     * @param Permission $permission
     * @return void
     */
    public function edit(Permission $permission): void
    {
        // TODO: 권한 체크

        $this->dispatch('open-edit-permission', permissionId: $permission->id);
    }

    /**
     * 권한 생성, 수정 성공 시 처리
     */
    #[On('permission-created')]
    #[On('permission-updated')]
    public function refreshAfterCreate()
    {
        $this->resetPage();
    }
}