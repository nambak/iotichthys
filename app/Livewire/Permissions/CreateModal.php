<?php

namespace App\Livewire\Permissions;

use App\Models\Permission;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CreateModal extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:255|unique:permissions,slug')]
    public string $slug = '';

    #[Validate('required|string|max:255')]
    public string $resource = '';

    #[Validate('required|string|max:255')]
    public string $action = '';

    #[Validate('nullable|string')]
    public string $description = '';

    public function render()
    {
        return view('livewire.permissions.create-modal');
    }

    /**
     * 권한 생성
     */
    public function create(): void
    {
        $this->validate();

        Permission::create([
            'name' => $this->name,
            'slug' => $this->slug,
            'resource' => $this->resource,
            'action' => $this->action,
            'description' => $this->description,
        ]);

        $this->reset();
        $this->dispatch('permission-created');
        $this->dispatch('modal-close', name: 'create-permission');
    }

    /**
     * 모달 취소
     */
    public function cancel(): void
    {
        $this->reset();
        $this->dispatch('modal-close', name: 'create-permission');
    }

    /**
     * 이름에서 슬러그 자동 생성
     */
    public function updatedName(): void
    {
        $this->slug = strtolower(str_replace(' ', '_', trim($this->name)));
    }
}