<?php

namespace App\Livewire\Organization;

use App\Models\Organization;
use Livewire\Component;

class DetailCard extends Component
{
    public Organization $organization;

    public function mount(Organization $organization)
    {
        $this->organization = $organization;
    }

    public function render()
    {
        return view('livewire.organization.detail-card');
    }
}
