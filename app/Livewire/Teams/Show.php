<?php

namespace App\Livewire\Teams;

use App\Models\Team;
use Livewire\Component;

class Show extends Component
{
    public Team $team;

    public function mount(Team $team)
    {
        $this->team = $team;
    }

    public function render()
    {
        return view('livewire.teams.show');
    }
}
