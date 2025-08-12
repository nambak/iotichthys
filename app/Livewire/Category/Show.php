<?php

namespace App\Livewire\Category;

use App\Models\Category;
use Livewire\Attributes\On;
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
     * 하위 카테고리 삭제
     *
     * @param int $categoryId
     * @return void
     */
    #[On('delete-subcategory')]
    public function deleteSubcategory($categoryId): void
    {
        $subcategory = Category::findOrFail($categoryId);
        
        // 삭제하려는 카테고리가 현재 카테고리의 직접 하위인지 확인
        if ($subcategory->parent_id !== $this->category->id) {
            $this->dispatch('show-error-toast', [
                'message' => __('권한이 없습니다.')
            ]);
            return;
        }
        
        // 카테고리 삭제 가능 여부 확인
        if (!$subcategory->canBeDeleted()) {
            $this->dispatch('show-error-toast', [
                'message' => __('하위 카테고리가 있어 삭제할 수 없습니다.')
            ]);
            return;
        }

        $subcategory->delete();
        
        $this->dispatch('subcategory-deleted');
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

    /**
     * 하위 카테고리 생성 모달 열기
     */
    public function createSubcategory(): void
    {
        $this->dispatch('open-create-subcategory', parentId: $this->category->id);
    }

    /**
     * 하위 카테고리 생성 성공 시 처리
     */
    #[On('subcategory-created')]
    public function refreshAfterCreate()
    {
        $this->resetPage();
    }

    /**
     * 하위 카테고리 수정 성공 시 처리
     */
    #[On('subcategory-updated')]
    public function refreshAfterUpdate()
    {
        $this->resetPage();
    }
}