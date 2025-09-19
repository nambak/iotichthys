<?php

namespace App\Livewire\Organization;

use App\Exceptions\UserAlreadyUserOfOrganizationException;
use App\Http\Requests\Organization\SearchUserRequest;
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
            $request = new SearchUserRequest;
            $this->validate($request->rules(), $request->messages());

            $this->foundUser = User::findByEmailOrFail($this->email);

            // 사용자가 다른 조직에 속해있는지 확인
            $hasAnyOrganization = $this->foundUser->organizations()->count() > 0;
            if ($hasAnyOrganization) {
                $existingOrg = $this->foundUser->organizations()->first();
                throw new UserAlreadyUserOfOrganizationException("이미 '{$existingOrg->name}' 조직에 속해있는 사용자입니다.");
            }

            $this->foundUser->ensureNotMemberOf($this->organization);

            $this->canAddUser = true;

        } catch (ModelNotFoundException) {
            $this->addError('email', '해당 이메일로 가입된 사용자를 찾을 수 없습니다.');
        } catch (UserAlreadyUserOfOrganizationException $e) {
            $this->addError('email', $e->getMessage() ?: '해당 사용자는 이미 조직에 속해있습니다.');
        } catch (\Exception $e) {
            logger("Unexpected error in searchUser: " . $e->getMessage());
            $this->addError('email', '사용자 검색 중 오류가 발생했습니다: ' . $e->getMessage());
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
        $this->resetErrorBag();
    }
}
