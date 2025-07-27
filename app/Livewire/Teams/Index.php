<?php

namespace App\Livewire\Teams;

use App\Models\Team;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        $teams = Team::with('organization')
            ->withCount('users')
            ->paginate(10);

        return view('livewire.teams.index', compact('teams'))
            ->layout('components.layouts.app', ['title' => '팀 관리']);
    }

    /**
     * 팀 삭제
     *
     * @param int $teamId
     * @return void
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
}