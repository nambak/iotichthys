<?php

namespace App\Livewire\Permissions;

use App\Models\Permission;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;  
use Livewire\Component;

class EditModal extends Component
{
    public ?Permission $permission = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:255')]
    public string $slug = '';

    #[Validate('required|string|max:255')]
    public string $resource = '';

    #[Validate('required|string|max:255')]
    public string $action = '';

    #[Validate('nullable|string')]
    public string $description = '';

    public function render()
    {
        return view('livewire.permissions.edit-modal');
    }

    /**
     * 권한 편집 모달 열기
     */
    #[On('open-edit-permission')]
    public function openEditModal($permissionId): void
    {
        $this->permission = Permission::findOrFail($permissionId);
        
        $this->name = $this->permission->name;
        $this->slug = $this->permission->slug;
        $this->resource = $this->permission->resource;
        $this->action = $this->permission->action;
        $this->description = $this->permission->description ?? '';

        $this->dispatch('modal-open', name: 'edit-permission');
    }

    /**
     * 권한 수정
     */
    public function update(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:permissions,slug,' . $this->permission->id,
            'resource' => 'required|string|max:255',
            'action' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $this->permission->update([
            'name' => $this->name,
            'slug' => $this->slug,
            'resource' => $this->resource,
            'action' => $this->action,
            'description' => $this->description,
        ]);

        $this->reset();
        $this->dispatch('permission-updated');
        $this->dispatch('modal-close', name: 'edit-permission');
    }

    /**
     * 모달 취소
     */
    public function cancel(): void
    {
        $this->reset();
        $this->dispatch('modal-close', name: 'edit-permission');
    }

    /**
     * 이름에서 슬러그 자동 생성
     */
    public function updatedName(): void
    {
        $this->slug = strtolower(str_replace(' ', '_', trim($this->name)));
    }
}