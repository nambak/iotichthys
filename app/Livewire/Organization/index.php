<?php

namespace App\Livewire\Organization;

use App\Models\Organization;
use Livewire\Component;
use Livewire\WithPagination;

class index extends Component
{
    use WithPagination;

    public $isCreating = false;
    public $isEditing = false;
    public $editingId = null;

    // 생성 및 수정 폼을 위한 변수
    public $name = '';
    public $description = '';


    public function render()
    {
        $organizations = Organization::paginate(10);

        return view('livewire.organization.index', compact('organizations'));
    }

    // 조직 생성 모달 표시
    public function create()
    {
        $this->resetInputFields();
        $this->isCreating = true;
    }

    // 폼 초기화
    private function resetInputFields()
    {
        $this->name = '';
        $this->description = '';
        $this->isCreating = false;
        $this->isEditing = false;
        $this->editingId = null;
    }
}