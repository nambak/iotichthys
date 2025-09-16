<?php

namespace App\Livewire\Teams;

use Exception;
use Flux\Flux;
use Livewire\Component;
use App\Models\Team;
use App\Models\User;
use App\Exceptions\UserAlreadyMemberOfTeamException;
use App\Http\Requests\Organization\SearchUserRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AddTeamMemberModal extends Component
{
    public Team $team;
    public ?User $foundUser = null;
    public string $email = '';
    public bool $canAddUser = false;
    public bool $showAddTeamMemberModal = false;

    public function mount(Team $team): void
    {
        $this->team = $team;
    }

    public function render()
    {
        return view('livewire.teams.add-team-member-modal');
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

            $this->foundUser->ensureNotMemberOf($this->team);

            $this->canAddUser = true;

        } catch (ModelNotFoundException) {
            $this->addError('email', '해당 이메일로 가입된 사용자를 찾을 수 없습니다.');
        } catch (UserAlreadyMemberOfTeamException) {
            $this->addError('email', '해당 사용자는 이미 이 팀에 속해있습니다.');
        }
    }

    /**
     * 팀에 멤버 추가
     *
     * @return void
     */
    public function addMemberToTeam(): void
    {
        if (!$this->foundUser) {
            $this->addError('email', '먼저 사용자를 검색해주세요.');
            return;
        }

        try {
            $this->team->users()->attach($this->foundUser->id);

            $this->dispatch('user-added-to-team', [
                'message' => $this->foundUser->name . '님이 팀에 추가되었습니다.',
            ]);
        } catch (Exception $e) {
            $this->addError('email', '팀에 사용자를 추가하는 중 오류가 발생했습니다.');
        } finally {
            $this->resetSearch();
            Flux::modals()->close('add-team-member-modal');
        }
    }

    /**
     * 검색 결과 초기화
     */
    public function resetSearch(): void
    {
        $this->email = '';
        $this->foundUser = null;
        $this->canAddUser = false;
        $this->resetErrorBag('email');
    }

    /**
     * 사용자 추가 모달 열기
     *
     * @return void
     */
    public function openAddTeamMemberModal(): void
    {
        $this->showAddTeamMemberModal = true;
        $this->resetSearch();
    }

    /**
     * 사용자 추가 모달 닫기
     *
     * @return void
     */
    public function closeAddTeamMemberModal(): void
    {
        $this->showAddTeamMemberModal = false;
        $this->resetSearch();
    }
}
