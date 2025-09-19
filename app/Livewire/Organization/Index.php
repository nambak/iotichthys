<?php

namespace App\Livewire\Organization;

use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.organization.index');
    }
}
