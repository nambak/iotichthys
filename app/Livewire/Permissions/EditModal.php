<?php

namespace App\Livewire\Permissions;

use App\Http\Requests\Permission\PermissionRequest;
use App\Models\Permission;
use Livewire\Attributes\On;
use Livewire\Component;

class EditModal extends Component
{
    public ?Permission $permission = null;

    public string $name = '';

    public string $action = '';

    public string $resource = '';

    public string $description = '';

    public function render()
    {
        return view('livewire.permissions.edit-modal');
    }

    /**
     * 권한 편집 모달 열기
     *
     * @param  int  $permissionId
     */
    #[On('open-edit-permission')]
    public function openEditModal($permissionId): void
    {
        $this->permission = Permission::findOrFail($permissionId);

        $this->name = $this->permission->name;
        $this->resource = $this->permission->resource;
        $this->action = $this->permission->action;
        $this->description = $this->permission->description ?? '';

        $this->modal('edit-permission')->show();
    }

    /**
     * 권한 수정
     */
    public function update(): void
    {
        $request = new PermissionRequest;

        $validatedData = $this->validate($request->rules(), $request->messages());

        $this->permission->update($validatedData);

        $this->modal('edit-permission')->close();

        $this->dispatch('permission-updated');

        $this->resetForm();
    }

    /**
     * 폼 리셋
     *
     * @return void
     */
    public function resetForm()
    {
        $this->reset();
        $this->resetValidation();
    }
}
