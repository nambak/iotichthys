<?php

namespace App\Livewire\Category;

use App\Models\Category;
use Livewire\Component;

class CategoryBreadcrumb extends Component
{
    public Category $category;

    public function mount(Category $category)
    {
        $this->category = $category;
    }

    public function render()
    {
        $breadcrumbs = $this->getBreadcrumbs();
        return view('livewire.category.category-breadcrumb', compact('breadcrumbs'));
    }

    /**
     * 카테고리 경로 생성
     */
    private function getBreadcrumbs(): array
    {
        $breadcrumbs = [];
        $current = $this->category;

        // 현재 카테고리부터 루트까지 거슬러 올라가며 경로 수집
        while ($current) {
            array_unshift($breadcrumbs, [
                'id' => $current->id,
                'name' => $current->name,
                'url' => route('category.show', $current)
            ]);
            $current = $current->parent;
        }

        return $breadcrumbs;
    }
}