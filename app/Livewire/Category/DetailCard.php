<?php

namespace App\Livewire\Category;

use App\Models\Category;
use Livewire\Component;

class DetailCard extends Component
{
    public Category $category;

    public function mount(Category $category)
    {
        $this->category = $category;
    }

    public function render()
    {
        return view('livewire.category.detail-card');
    }
}

