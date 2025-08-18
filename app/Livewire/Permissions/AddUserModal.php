<?php

namespace App\Livewire\Permissions;

use App\Http\Requests\Organization\SearchUserRequest;
use App\Models\Permission;
use App\Models\User;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

class AddUserModal extends Component
{
    use WithPagination;

    public ?User $foundUser = null;

    public ?Permission $permission = null;

    public string $email = '';

    public function mount(Permission $permission)
    {
        $this->permission = $permission;
    }

    public function render()
    {
        return view('livewire.permissions.add-user-modal');
    }

    /**
     * 사용자 검색
     */
    public function searchUser(): void
    {
        $request = new SearchUserRequest;
        $this->validate($request->rules(), $request->messages());

        $this->foundUser = User::where('email', $this->email)->first();

        if (! $this->foundUser) {
            $this->addError('email', '해당 이메일로 가입된 사용자를 찾을 수 없습니다.');

            return;
        }

        // 이미 이 권한을 가진 사용자인지 확인 (직접 할당된 권한과 역할을 통한 권한 모두 확인)
        $hasDirectPermission = $this->foundUser->permissions()->where('permission_id', $this->permission->id)->exists();
        $hasRolePermission = $this->foundUser->roles()->whereHas('permissions', function ($query) {
            $query->where('permission_id', $this->permission->id);
        })->exists();

        if ($hasDirectPermission || $hasRolePermission) {
            $this->addError('email', '해당 사용자는 이미 이 권한을 가지고 있습니다.');
        }
    }

    /**
     * 권한에 사용자 추가
     */
    public function addUserToPermission(): void
    {
        if (! $this->foundUser) {
            $this->addError('email', '먼저 사용자를 검색해주세요.');

            return;
        }

        $this->permission->users()->attach($this->foundUser->id);

        $this->dispatch('user-added-to-permission', [
            'message' => $this->foundUser->name.'님에게 권한이 추가되었습니다.',
        ]);

        $this->resetUserSearch();

        Flux::modals()->close('add-user-modal');
    }

    /**
     * 사용자 검색 초기화
     */
    public function resetUserSearch(): void
    {
        $this->email = '';
        $this->foundUser = null;

        $this->resetValidation();
    }

    /**
     * 사용자 추가 모달 닫기
     */
    public function closeAddUserModal(): void
    {
        $this->resetUserSearch();
    }
}
