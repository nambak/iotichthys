<?php

namespace App\Livewire\Organization;

use App\Exceptions\UserAlreadyUserOfOrganizationException;
use App\Models\Organization;
use App\Models\User;
use Exception;
use Flux\Flux;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Component;
use Livewire\WithPagination;

class AddUserModal extends Component
{
    use WithPagination;

    public bool $showAddUserModal = false;
    public ?Organization $organization = null;
    public string $email = '';
    public ?User $foundUser = null;
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
     */
    public function searchUser(): void
    {
        $this->canAddUser = false;

        try {
            $request = new \App\Http\Requests\Organization\SearchUserRequest;
            $this->validate($request->rules(), $request->messages());

            $this->foundUser = User::findByEmailOrFail($this->email);

            $this->foundUser->ensureNotMemberOf($this->organization);

            $this->canAddUser = true;

        } catch (ModelNotFoundException) {
            $this->addError('email', '해당 이메일로 가입된 사용자를 찾을 수 없습니다.');
        } catch (UserAlreadyUserOfOrganizationException) {
            $this->addError('email', '해당 사용자는 이미 이 조직에 속해있습니다.');
        }
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

        try {
            $this->organization->users()->attach($this->foundUser->id, [
                'is_owner' => false,
            ]);

            $this->dispatch('user-added-to-organization', [
                'message' => $this->foundUser->name.'님이 조직에 추가되었습니다.',
            ]);

        } catch (Exception $e) {
            $this->addError('email', '조직에 사용자를 추가하는 중 오류가 발생했습니다.');
        } finally {
            $this->reset();
            Flux::modals()->close('add-user-modal');
        }
    }

    /**
     * 검색 결과 초기화
     */
    public function reset(): void
    {
        $this->email = '';
        $this->foundUser = null;
        $this->canAddUser = false;
        $this->resetErrorBag('email');
    }

    /**
     * 사용자 추가 모달 열기
     */
    public function openAddUserModal(): void
    {
        $this->showAddUserModal = true;
        $this->reset();
    }

    /**
     * 사용자 추가 모달 닫기
     */
    public function closeAddUserModal(): void
    {
        $this->showAddUserModal = false;
        $this->reset();
    }
}
