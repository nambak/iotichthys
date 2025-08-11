<?php

namespace App\Livewire\Permissions;

use App\Http\Requests\Permission\PermissionRequest;
use App\Models\Permission;
use Livewire\Component;

class CreateModal extends Component
{
    public string $name = '';
    public string $resource = '';
    public string $action = '';
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
        // Form Request 클래스에서 validation rules와 messages 가져오기
        $request = new PermissionRequest();
        $validatedData = $this->validate($request->rules(), $request->messages());

        // slug 자동 생성
        $validatedData['slug'] = strtolower(str_replace(' ', '_', $this->resource . '_' . $this->action));

        Permission::create($validatedData);

        $this->modal('create-permission')->close();

        $this->dispatch('permission-created');

        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['name', 'resource', 'action', 'description']);

        $this->resetValidation();
    }
}