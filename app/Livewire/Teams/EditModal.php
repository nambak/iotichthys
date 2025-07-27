<?php

namespace App\Livewire\Teams;

use App\Http\Requests\UpdateTeamRequest;
use App\Models\Team;
use Livewire\Attributes\On;
use Livewire\Component;

class EditModal extends Component
{
    public ?Team $team = null;

    public string $name = '';

    public string $slug = '';

    public string $description = '';

    /**
     * @return \Illuminate\Contracts\View\Factory|
     *         \Illuminate\Contracts\View\View|
     *         \Illuminate\Foundation\Application|
     *         object
     */
    public function render()
    {
        return view('livewire.teams.edit-modal');
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

        $this->resetValidation();
        $this->modal('edit-team')->show();
    }

    /**
     * 팀 수정
     *
     * @return void
     */
    public function save()
    {
        if (! $this->team) {
            return;
        }

        //  TODO: 권한 체크

        $request = new UpdateTeamRequest;

        request()->merge(['team_id' => $this->team->id]);

        try {
            $validatedData = $this->validate($request->rules(), $request->messages());
            \Log::info('EditModal: Validation passed', $validatedData);

            $this->team->update($validatedData);
            \Log::info('EditModal: Team updated successfully');

            $this->modal('edit-team')->close();
            \Log::info('EditModal: Modal closed');

            // 이벤트 발생 전 로그
            \Log::info('EditModal: About to dispatch team-updated event');
            $this->dispatch('team-updated');
            \Log::info('EditModal: team-updated event dispatched');

        } catch (\Exception $e) {
            \Log::error('EditModal: Error in save method', ['error' => $e->getMessage()]);
            session()->flash('error', 'An error occurred while updating the team.');
        }
    }

    /**
     * 폼 리셋
     *
     * @return void
     */
    public function resetForm()
    {
        $this->team = null;
        $this->name = '';
        $this->slug = '';
        $this->description = '';
        $this->resetValidation();
    }
}
