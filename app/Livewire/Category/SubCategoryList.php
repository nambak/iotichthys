<?php

namespace App\Livewire\Category;

use App\Models\Category;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class SubCategoryList extends Component
{
    use WithPagination;
    
    public Category $category;

    public function mount(Category $category) {
        $this->category = $category;
    }

    public function render()
    {
        // 현재 카테고리의 하위 카테고리들을 조회
        $subCategories = $this->category->children()
            ->withCount('children')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.category.sub-category-list', compact('subCategories'));
    }

    public function deleteSubcategory($categoryId)
    {
        $this->dispatch('delete-subcategory', categoryId: $categoryId);
    }

    public function editSubcategory($categoryId)
    {
        $this->dispatch('open-edit-category', categoryId: $categoryId);
    }

    /**
     * 하위 카테고리 삭제 후 페이지 리셋
     */
    #[On('subcategory-deleted')]
    public function refreshAfterDelete()
    {
        $this->resetPage();
    }

    /**
     * 하위 카테고리 생성 후 페이지 리셋
     */
    #[On('subcategory-created')]
    public function refreshAfterCreate()
    {
        $this->resetPage();
    }

    /**
     * 하위 카테고리 수정 후 페이지 리셋
     */
    #[On('subcategory-updated')]
    public function refreshAfterUpdate()
    {
        $this->resetPage();
    }
}
