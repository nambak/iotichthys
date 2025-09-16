<?php

namespace App\Livewire\Organization;

use App\Models\Organization;
use App\Traits\UserSearchTrait;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

class AddUserModal extends Component
{
    use WithPagination;
    use UserSearchTrait;

    public bool $showAddUserModal = false;

    public ?Organization $organization = null;

    public function mount(Organization $organization)
    {
        $this->organization = $organization;
    }

    public function render()
    {
        return view('livewire.organization.add-user-modal');
    }


    /**
     * 조직에 사용자 추가
     */
    public function addUserToOrganization(): void
    {
        if (! $this->foundUser) {
            $this->addError('email', '먼저 사용자를 검색해주세요.');

            return;
        }

        $this->organization->users()->attach($this->foundUser->id, [
            'is_owner' => false,
        ]);

        $this->dispatch('user-added-to-organization', [
            'message' => $this->foundUser->name.'님이 조직에 추가되었습니다.',
        ]);

        $this->resetUserSearch();

        Flux::modals()->close('add-user-modal');
    }

    /**
     * 사용자 검색 초기화
     */
    public function resetUserSearch(): void
    {
        $this->resetSearch();
    }

    /**
     * 사용자 추가 모달 열기
     */
    public function openAddUserModal(): void
    {
        $this->showAddUserModal = true;
        $this->resetUserSearch();
    }

    /**
     * 사용자 추가 모달 닫기
     */
    public function closeAddUserModal(): void
    {
        $this->showAddUserModal = false;
        $this->resetUserSearch();
    }
}
