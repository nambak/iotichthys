<?php

namespace App\Livewire\Organization;

use App\Http\Requests\Organization\SearchUserRequest;
use App\Models\Organization;
use App\Models\User;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

class AddUserModal extends Component
{
    use WithPagination;

    public ?User $foundUser = null;
    public string $email = '';
    public bool $showAddUserModal = false;
    public ?Organization $organization =  null;
    public bool $canAddUser = false;

    public function mount(Organization $organization)
    {
        $this->organization = $organization;
    }

    public function render()
    {
        return view('livewire.organization.add-user-modal');
    }

    /**
     * 사용자 검색
     *
     * @return void
     */
    public function searchUser(): void
    {
        $request = new SearchUserRequest();
        $this->validate($request->rules(), $request->messages());

        $this->foundUser = User::where('email', $this->email)->first();
        $this->canAddUser = false;

        if (!$this->foundUser) {
            $this->addError('email', '해당 이메일로 가입된 사용자를 찾을 수 없습니다.');
            return;
        }

        // 이미 조직에 속해있는 사용자인지 확인
        if ($this->organization->users()->where('user_id', $this->foundUser->id)->exists()) {
            $this->addError('email', '해당 사용자는 이미 이 조직에 속해있습니다.');
            return;
        }

        // 여기까지 왔다면 추가 가능한 사용자
        $this->canAddUser = true;
    }

    /**
     * 조직에 사용자 추가
     *
     * @return void
     */
    public function addUserToOrganization(): void
    {
        if (!$this->foundUser) {
            $this->addError('userEmail', '먼저 사용자를 검색해주세요.');
            return;
        }

        $this->organization->users()->attach($this->foundUser->id, [
            'is_owner' => false
        ]);

        $this->dispatch('user-added-to-organization', [
            'message' => $this->foundUser->name . '님이 조직에 추가되었습니다.'
        ]);

        $this->resetUserSearch();

        Flux::modals()->close('add-user-modal');
    }

    /**
     * 사용자 검색 초기화
     *
     * @return void
     */
    public function resetUserSearch(): void
    {
        $this->reset();
        $this->resetValidation();
    }

    /**
     * 사용자 추가 모달 열기
     *
     * @return void
     */
    public function openAddUserModal(): void
    {
        $this->showAddUserModal = true;
        $this->resetUserSearch();
    }

    /**
     * 사용자 추가 모달 닫기
     *
     * @return void
     */
    public function closeAddUserModal(): void
    {
        $this->showAddUserModal = false;
        $this->resetUserSearch();
    }
}
