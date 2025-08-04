<?php

namespace App\Livewire\Permissions;

use App\Http\Requests\Permission\PermissionRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Livewire\Component;

class CreateModal extends Component
{
    public string $name = '';
    public string $resource = '';
    public string $action = '';
    public string $description = '';
    public array $selectedUsers = [];

    public function render()
    {
        $users = User::where('id', '!=', auth()->id())
            ->orderBy('name')
            ->get();

        return view('livewire.permissions.create-modal', compact('users'));
    }

    /**
     * 권한 생성
     */
    public function create(): void
    {
        // Form Request 클래스에서 validation rules와 messages 가져오기
        $request = new PermissionRequest();
        $validatedData = $this->validate($request->rules(), $request->messages());

        // slug 자동 생성
        $validatedData['slug'] = strtolower(str_replace(' ', '_', $this->resource . '_' . $this->action));

        $permission = Permission::create($validatedData);

        // 선택된 사용자들이 있다면 자동으로 역할 생성하고 할당
        if (!empty($this->selectedUsers)) {
            $this->assignUsersToPermission($permission);
        }

        $this->modal('create-permission')->close();

        $this->dispatch('permission-created');

        $this->resetForm();
    }

    /**
     * 선택된 사용자들을 권한에 할당
     */
    private function assignUsersToPermission(Permission $permission): void
    {
        // 권한 전용 역할 생성 또는 찾기
        $roleName = $permission->name . ' Role';
        $roleSlug = $permission->slug . '_role';

        $role = Role::firstOrCreate(
            ['slug' => $roleSlug],
            [
                'name' => $roleName,
                'description' => '권한 "' . $permission->name . '"을 위해 자동 생성된 역할',
                'is_system_role' => false,
            ]
        );

        // 역할에 권한 할당
        if (!$role->permissions()->where('permission_id', $permission->id)->exists()) {
            $role->permissions()->attach($permission->id);
        }

        // 선택된 사용자들을 역할에 할당
        foreach ($this->selectedUsers as $userId) {
            $user = User::find($userId);
            if ($user && !$user->roles()->where('role_id', $role->id)->exists()) {
                $user->roles()->attach($role->id);
            }
        }
    }

    public function resetForm()
    {
        $this->reset(['name', 'resource', 'action', 'description', 'selectedUsers']);

        $this->resetValidation();
    }
}