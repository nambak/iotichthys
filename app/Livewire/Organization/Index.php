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

    /**
     * 조직 수정 성공 시 처리
     */
    #[On('organization-updated')]
    public function refreshAfterUpdate()
    {
        session()->flash('success', __('messages.organization_updated'));
    }

    /**
     * 조직 삭제
     *
     * @param Organization $organization
     * @return void
     */
    public function deleteOrganization(Organization $organization)
    {
        // TODO: 조직 삭제 권한 체크 필요
        // TODO: 조직에 연결된 팀이나 사용자가 있는지 확인
        
        $organization->delete();
        
        session()->flash('success', __('messages.organization_deleted'));
        $this->resetPage();
    }

    /**
     * 조직 편집 모달 열기
     *
     * @param Organization $organization
     * @return void
     */
    public function editOrganization(Organization $organization)
    {
        $this->dispatch('open-edit-organization', organizationId: $organization->id);
    }
}
