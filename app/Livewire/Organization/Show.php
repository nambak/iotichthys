<?php

namespace App\Livewire\Organization;

use App\Models\Organization;
use Livewire\Component;

class Show extends Component
{
    public Organization $organization;

    public function mount(Organization $organization)
    {
        $this->organization = $organization;
    }

    public function render()
    {
        $users = $this->organization->users()->paginate(10);

        return view('livewire.organization.show', [
            'users' => $users,
        ]);
    }
}
