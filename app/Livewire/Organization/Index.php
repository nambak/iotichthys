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
    }

    /**
     * 조직 삭제
     */
    public function delete($organizationId)
    {
        $organization = Organization::findOrFail($organizationId);
        
        // 조직에 속한 사용자가 있는지 확인
        if ($organization->users()->count() > 0) {
            $this->dispatch('show-error-toast', ['message' => __('messages.organization_delete_has_users')]);
            return;
        }

        $organization->delete();
        
        $this->dispatch('organization-deleted');
        $this->resetPage();
    }
}
