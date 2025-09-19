<?php

namespace App\Livewire\Teams;

use App\Models\Organization;
use App\Models\Team;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ListTable extends Component
{
    use WithPagination;

    public ?Organization $organization = null;
    public bool $showHeader = true;
    public bool $showOrganizationColumn = true;
    public bool $showActions = true;
    public bool $showPagination = true;

    public function mount(
        ?Organization $organization = null,
        bool $showHeader = true,
        bool $showOrganizationColumn = true,
        bool $showActions = true,
        bool $showPagination = true
    ) {
        $this->organization = $organization;
        $this->showHeader = $showHeader;
        $this->showOrganizationColumn = $showOrganizationColumn;
        $this->showActions = $showActions;
        $this->showPagination = $showPagination;
    }

    public function render()
    {
        // 완전히 새로운 쿼리로 테스트
        if ($this->organization) {
            // 특정 조직의 팀만
            $teams = Team::with('organization')
                ->withCount('users')
                ->where('organization_id', $this->organization->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            // 모든 팀
            $teams = Team::with('organization')
                ->withCount('users')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('livewire.teams.list-table', compact('teams'));
    }

    /**
     * 팀 삭제
     */
    public function delete($teamId): void
    {
        $team = Team::findOrFail($teamId);

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
