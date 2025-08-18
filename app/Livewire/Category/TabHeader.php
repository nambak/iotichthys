<?php

namespace App\Livewire\Category;

use Livewire\Component;

class TabHeader extends Component
{
    public $activeTab = 'overview';

    public $tabs = [];

    public function mount($activeTab = 'overview')
    {
        $this->activeTab = $activeTab;
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->js("window.dispatchEvent(new CustomEvent('tab-changed', { detail: { tab: '$tab' } }))");
    }

    public function render()
    {
        return view('livewire.category.tab-header');
    }
}
