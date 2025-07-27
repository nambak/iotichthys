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
     * 조직 삭제
     *
     * @param int $organizationId
     * @return void
     */
    public function delete($organizationId): void
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

    /**
     * 조직 삭제
     *
     * @param Organization $organization
     * @return void
     */
    public function deleteOrganization(Organization $organization): void
    {
        // 권한 체크
        if (!auth()->user()->can('delete', $organization)) {
            session()->flash('error', __('messages.organization_delete_unauthorized'));
            return;
        }
        
        // TODO: 조직에 연결된 팀이나 사용자가 있는지 확인
        
        $organization->delete();
        
        $this->resetPage();
    }

    /**
     * 조직 편집 모달 열기
     *
     * @param Organization $organization
     * @return void
     */
    public function editOrganization(Organization $organization): void
    {
        // TODO: 권한 체크

        $this->dispatch('open-edit-organization', organizationId: $organization->id);
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
     * 조직 수정 성공 시 처리
     */
    #[On('organization-updated')]
    public function refreshAfterUpdate()
    {
        $this->resetPage();
    }
}
