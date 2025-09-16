<?php

namespace App\Livewire\Teams;

use App\Models\Team;
use App\Traits\UserSearchTrait;
use Flux\Flux;
use Livewire\Component;

class AddTeamMemberModal extends Component
{
    use UserSearchTrait;

    public bool $showAddTeamMemberModal = false;
    public ?Team $team = null;

    public function mount(Team $team): void
    {
        $this->team = $team;
    }

    public function render()
    {
        return view('livewire.teams.add-team-member-modal');
    }


    /**
     * 팀에 멤버 추가
     */
    public function addMemberToTeam(): void
    {
        if (! $this->foundUser) {
            $this->addError('email', '먼저 사용자를 검색해주세요.');

            return;
        }

        $this->team->users()->attach($this->foundUser->id);

        $this->dispatch('user-added-to-team', [
            'message' => $this->foundUser->name.'님이 팀에 추가되었습니다.',
        ]);

        $this->resetSearch();

        Flux::modals()->close('add-team-member-modal');
    }

    /**
     * 사용자 추가 모달 열기
     */
    public function openAddTeamMemberModal(): void
    {
        $this->showAddTeamMemberModal = true;
        $this->resetSearch();
    }

    /**
     * 사용자 추가 모달 닫기
     */
    public function closeAddTeamMemberModal(): void
    {
        $this->showAddTeamMemberModal = false;
        $this->resetSearch();
    }
}
