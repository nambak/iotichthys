<?php

namespace App\Livewire\Teams;

use App\Http\Requests\Team\TeamRequest;
use App\Models\Organization;
use App\Models\Team;
use Livewire\Component;

class CreateModal extends Component
{
    public string $organization_id = '';

    public string $name = '';

    public string $slug = '';

    public string $description = '';

    public function render()
    {
        $organizations = Organization::orderBy('name')->get();

        return view('livewire.teams.create-modal', compact('organizations'));
    }

    /**
     * 팀 생성
     *
     * @return void
     */
    public function save()
    {
        // Form Request 클래스에서 validation rules와 messages 가져오기
        $request = new TeamRequest;
        $validatedData = $this->validate($request->rules(), $request->messages());

        Team::create($validatedData);

        $this->modal('create-team')->close();

        $this->dispatch('team-created');

        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset();

        $this->resetValidation();
    }
}
