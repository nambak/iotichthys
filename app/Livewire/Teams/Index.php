<?php

namespace App\Livewire\Teams;

use App\Models\Team;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        $teams = Team::with('organization')
            ->withCount('users')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.teams.index', compact('teams'));
    }

    /**
     * 팀 삭제
     *
     * @param  int  $teamId
     */
    public function delete($teamId): void
    {
        $team = Team::findOrFail($teamId);

        // 팀에 속한 사용자가 있는지 확인
        if ($team->users()->count() > 0) {
            $this->dispatch('show-error-toast', ['message' => '팀에 속한 사용자가 있어 삭제할 수 없습니다.']);

            return;
        }

        $team->delete();

        $this->dispatch('team-deleted');
        $this->resetPage();
    }

    /**
     * 팀 편집 모달 열기
     */
    public function editTeam(Team $team): void
    {
        // TODO: 권한 체크

        $this->dispatch('open-edit-team', teamId: $team->id);
    }

    /**
     * 팀 생성 성공 시 처리
     */
    #[On('team-created')]
    public function refreshAfterCreate()
    {
        $this->resetPage();
    }

    /**
     * 팀 수정 성공 시 처리
     */
    #[On('team-updated')]
    public function refreshAfterUpdate()
    {
        $this->resetPage();
    }
}
