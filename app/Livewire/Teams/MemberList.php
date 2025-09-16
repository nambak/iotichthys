<?php

namespace App\Livewire\Teams;

use App\Models\Team;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class MemberList extends Component
{
    use WithPagination;

    public Team $team;

    public function mount(Team $team)
    {
        $this->team = $team;
    }

    public function render()
    {
        $members = $this->team->users()->paginate(10);

        return view('livewire.teams.member-list', compact('members'));
    }

    /**
     * 팀에서 사용자 제거
     *
     * @param int $userId
     * @return void
     */
    public function removeUserFromTeam(int $userId): void
    {
        $user = User::findOrFail($userId);

        $this->team->users()->detach($userId);

        $this->dispatch('user-removed-from-team', [
            'message' => $user->name . '님이 팀에서 제거되었습니다.',
        ]);

        $this->resetPage();
    }

    #[On('user-added-to-team')]
    public function refreshAfterCreate(): void
    {
        $this->resetPage();
    }
}
