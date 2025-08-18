<?php

namespace App\Livewire\Teams;

use App\Http\Requests\Team\TeamRequest;
use App\Models\Organization;
use App\Models\Team;
use Illuminate\Contracts\View\Factory;
use Livewire\Attributes\On;
use Livewire\Component;

class EditModal extends Component
{
    public $team;

    public string $name = '';

    public string $slug = '';

    public string $description = '';

    public int $organization_id = 0;

    /**
     * @return Factory|
     *         \Illuminate\Contracts\View\View|
     *         \Illuminate\Foundation\Application|
     *         object
     */
    public function render()
    {
        $organizations = Organization::orderBy('name')->get();

        return view('livewire.teams.edit-modal', compact('organizations'));
    }

    /**
     * 팀 편집 모달 열기 (이벤트 리스너)
     *
     * @param  int  $teamId
     * @return void
     */
    #[On('open-edit-team')]
    public function openEdit($teamId)
    {
        $team = Team::findOrFail($teamId);

        // TODO: 권한 체크

        $this->team = $team;
        $this->name = $team->name;
        $this->slug = $team->slug;
        $this->description = $team->description ?? '';
        $this->organization_id = $team->organization_id;

        $this->resetValidation();
        $this->modal('edit-team')->show();
    }

    /**
     * 팀 수정
     *
     * @return void
     */
    public function update()
    {
        if (! $this->team) {
            return;
        }

        //  TODO: 권한 체크

        $request = new TeamRequest;

        $validatedData = $this->validate($request->rules(), $request->messages());

        $this->team->update($validatedData);

        $this->modal('edit-team')->close();

        $this->dispatch('team-updated');

        $this->resetForm();
    }

    /**
     * 폼 리셋
     *
     * @return void
     */
    public function resetForm()
    {
        $this->resetValidation();
    }
}
