<?php

namespace App\Livewire\Organization;

use App\Models\Organization;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        $organizations = Organization::withCount('users')->paginate(10);

        return view('livewire.organization.index', compact('organizations'));
    }

    /**
     * 조직 생성 성공 시 처리
     */
    #[On('organization-created')]
    public function refreshAfterCreate()
    {
        $this->resetPage();
        session()->flash('success', __('messages.organization_created'));
    }
}
