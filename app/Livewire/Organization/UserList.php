<?php

namespace App\Livewire\Organization;

use App\Models\Organization;
use Livewire\Component;

class UserList extends Component
{
    public $organization;

    public function mount(Organization $organization)
    {
        $this->organization = $organization;
    }

    public function render()
    {
        $users = $this->organization->users()->paginate(10);

        return view('livewire.organization.user-list', compact('users'));
    }
}
