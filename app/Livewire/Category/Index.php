<?php

namespace App\Livewire\Category;

use App\Models\Category;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        // 최상위 카테고리만 조회하되, 하위 카테고리 개수도 함께 조회
        $categories = Category::topLevel()
            ->withCount('children')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.category.index', compact('categories'));
    }

    /**
     * 카테고리 삭제
     *
     * @param  int  $categoryId
     */
    public function delete($categoryId): void
    {
        $category = Category::findOrFail($categoryId);

        // 카테고리 삭제 가능 여부 확인
        if (! $category->canBeDeleted()) {
            $this->dispatch('show-error-toast', [
                'message' => __('하위 카테고리가 있어 삭제할 수 없습니다.'),
            ]);

            return;
        }

        $category->delete();

        $this->dispatch('category-deleted');
        $this->resetPage();
    }

    /**
     * 카테고리 편집 모달 열기
     */
    public function editCategory(Category $category): void
    {
        $this->dispatch('open-edit-category', categoryId: $category->id);
    }

    /**
     * 카테고리 생성 성공 시 처리
     */
    #[On('category-created')]
    public function refreshAfterCreate()
    {
        $this->resetPage();
    }

    /**
     * 카테고리 수정 성공 시 처리
     */
    #[On('category-updated')]
    public function refreshAfterUpdate()
    {
        $this->resetPage();
    }
}
