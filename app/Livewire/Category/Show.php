<?php

namespace App\Livewire\Category;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;

    public Category $category;

    public function mount(Category $category)
    {
        $this->category = $category;
    }

    public function render()
    {
        return view('livewire.category.show');
    }

    /**
     * 하위 카테고리 편집 모달 열기
     *
     * @param Category $subcategory
     * @return void
     */
    public function editSubcategory(Category $subcategory): void
    {
        $this->dispatch('open-edit-category', categoryId: $subcategory->id);
    }
}