<?php

namespace App\Livewire\Organization;

use App\Models\Organization;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;

    public Organization $organization;
    public string $userEmail = '';
    public bool $showAddUserModal = false;
    public ?User $foundUser = null;

    public function mount(Organization $organization)
    {
        $this->organization = $organization;
    }

    public function render()
    {
        $users = $this->organization->users()->paginate(10);
        
        return view('livewire.organization.show', [
            'users' => $users
        ]);
    }

    /**
     * 사용자 검색
     */
    public function searchUser()
    {
        $this->validate([
            'userEmail' => 'required|email'
        ], [
            'userEmail.required' => '이메일을 입력해주세요.',
            'userEmail.email' => '올바른 이메일 형식을 입력해주세요.'
        ]);

        $this->foundUser = User::where('email', $this->userEmail)->first();

        if (!$this->foundUser) {
            $this->addError('userEmail', '해당 이메일로 가입된 사용자를 찾을 수 없습니다.');
            return;
        }

        // 이미 조직에 속해있는 사용자인지 확인
        if ($this->organization->users()->where('user_id', $this->foundUser->id)->exists()) {
            $this->addError('userEmail', '해당 사용자는 이미 이 조직에 속해있습니다.');
            return;
        }
    }

    /**
     * 조직에 사용자 추가
     */
    public function addUserToOrganization()
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
        $this->resetPage();
    }

    /**
     * 조직에서 사용자 제거
     */
    public function removeUserFromOrganization($userId)
    {
        $user = User::findOrFail($userId);
        
        // 조직 소유자는 제거할 수 없음
        $userOrganization = $this->organization->users()->where('user_id', $userId)->first();
        if ($userOrganization && $userOrganization->pivot->is_owner) {
            $this->dispatch('show-error-toast', [
                'message' => '조직 소유자는 제거할 수 없습니다.'
            ]);
            return;
        }

        $this->organization->users()->detach($userId);

        $this->dispatch('user-removed-from-organization', [
            'message' => $user->name . '님이 조직에서 제거되었습니다.'
        ]);

        $this->resetPage();
    }

    /**
     * 사용자 검색 초기화
     */
    public function resetUserSearch()
    {
        $this->userEmail = '';
        $this->foundUser = null;
        $this->resetErrorBag();
    }

    /**
     * 사용자 추가 모달 열기
     */
    public function openAddUserModal()
    {
        $this->showAddUserModal = true;
        $this->resetUserSearch();
    }

    /**
     * 사용자 추가 모달 닫기
     */
    public function closeAddUserModal()
    {
        $this->showAddUserModal = false;
        $this->resetUserSearch();
    }
}