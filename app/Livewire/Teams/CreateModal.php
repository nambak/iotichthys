<?php

namespace App\Livewire\Teams;

use App\Http\Requests\TeamRequest;
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
        // Form Request를 사용한 validation
        $request = new TeamRequest();
        $validatedData = $this->validate($request->rules(), $request->messages());

        Team::create($validatedData);

        $this->modal('create-team')->close();

        $this->dispatch('team-created');
    }

    public function resetForm()
    {
        $this->reset([
            'organization_id',
            'name',
            'slug',
            'description'
        ]);

        $this->resetValidation();
    }
}