<?php

namespace App\Livewire\Teams;

use App\Models\Organization;
use App\Models\Team;
use Livewire\Component;

class DetailCard extends Component
{
    public Organization $organization;
    public Team $team;

    public function mount(Team $team)
    {
        $this->team = $team;
        $this->organization = $team->organization;
    }

    public function render()
    {
        return view('livewire.teams.detail-card');
    }
}
