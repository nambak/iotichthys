<?php

namespace App\Livewire\Permissions;

use App\Http\Requests\Organization\SearchUserRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

class AddUserModal extends Component
{
    use WithPagination;

    public ?User $foundUser = null;
    public string $email = '';
    public bool $showAddUserModal = false;
    public ?Permission $permission = null;
    public bool $canAddUser = false;

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
     *
     * @return void
     */
    public function searchUser(): void
    {
        $request = new SearchUserRequest();
        $this->validate($request->rules(), $request->messages());

        $this->foundUser = User::where('email', $this->email)->first();
        $this->canAddUser = false;

        if (!$this->foundUser) {
            $this->addError('email', '해당 이메일로 가입된 사용자를 찾을 수 없습니다.');
            return;
        }

        // 이미 이 권한을 가진 사용자인지 확인
        $hasPermission = $this->foundUser->roles()->whereHas('permissions', function ($query) {
            $query->where('permission_id', $this->permission->id);
        })->exists();

        if ($hasPermission) {
            $this->addError('email', '해당 사용자는 이미 이 권한을 가지고 있습니다.');
            return;
        }

        // 여기까지 왔다면 추가 가능한 사용자
        $this->canAddUser = true;
    }

    /**
     * 권한에 사용자 추가
     *
     * @return void
     */
    public function addUserToPermission(): void
    {
        if (!$this->foundUser) {
            $this->addError('email', '먼저 사용자를 검색해주세요.');
            return;
        }

        // 권한 전용 역할 생성 또는 찾기
        $roleName = $this->permission->name . ' Role';
        $roleSlug = $this->permission->slug . '_role';

        $role = Role::firstOrCreate(
            ['slug' => $roleSlug],
            [
                'name' => $roleName,
                'description' => '권한 "' . $this->permission->name . '"을 위해 자동 생성된 역할',
                'is_system_role' => false,
            ]
        );

        // 역할에 권한 할당 (이미 있는지 확인)
        if (!$role->permissions()->where('permission_id', $this->permission->id)->exists()) {
            $role->permissions()->attach($this->permission->id);
        }

        // 사용자를 역할에 할당
        if (!$this->foundUser->roles()->where('role_id', $role->id)->exists()) {
            $this->foundUser->roles()->attach($role->id);
        }

        $this->dispatch('user-added-to-permission', [
            'message' => $this->foundUser->name . '님에게 권한이 추가되었습니다.'
        ]);

        $this->resetUserSearch();

        Flux::modals()->close('add-user-modal');
    }

    /**
     * 사용자 검색 초기화
     *
     * @return void
     */
    public function resetUserSearch(): void
    {
        $this->reset();
        $this->resetValidation();
    }

    /**
     * 사용자 추가 모달 열기
     *
     * @return void
     */
    public function openAddUserModal(): void
    {
        $this->showAddUserModal = true;
        $this->resetUserSearch();
    }

    /**
     * 사용자 추가 모달 닫기
     *
     * @return void
     */
    public function closeAddUserModal(): void
    {
        $this->showAddUserModal = false;
        $this->resetUserSearch();
    }
}