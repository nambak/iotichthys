<?php

namespace App\Livewire\Permissions;

use App\Models\Permission;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;

    public Permission $permission;

    public function mount(Permission $permission)
    {
        $this->permission = $permission;
    }

    public function render()
    {
        $users = $this->permission->users()->paginate(10);

        return view('livewire.permissions.show', [
            'users' => $users,
        ]);
    }

    /**
     * 권한에서 사용자 제거
     */
    public function removeUserFromPermission($userId)
    {
        $user = User::findOrFail($userId);

        // permission_user 테이블에서 직접 관계 제거
        $this->permission->users()->detach($userId);

        $this->dispatch('user-removed-from-permission', [
            'message' => $user->name.'님의 권한이 제거되었습니다.',
        ]);

        $this->resetPage();
    }

    #[On('user-added-to-permission')]
    public function refreshAfterCreate()
    {
        $this->resetPage();
    }
}
