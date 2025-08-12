<?php

namespace App\Livewire\Category;

use App\Http\Requests\Category\CategoryRequest;
use App\Models\Category;
use Livewire\Attributes\On;
use Livewire\Component;

class EditModal extends Component
{
    public ?Category $category = null;
    public string $name = '';
    public string $description = '';
    public ?int $parent_id = null;
    public int $sort_order = 0;
    public bool $is_active = true;

    public function render()
    {
        return view('livewire.category.edit-modal');
    }

    /**
     * 카테고리 편집 모달 열기
     *
     * @param int $categoryId
     * @return void
     */
    #[On('open-edit-category')]
    public function openEditModal($categoryId): void
    {
        $this->category = Category::findOrFail($categoryId);

        $this->name = $this->category->name;
        $this->description = $this->category->description ?? '';
        $this->parent_id = $this->category->parent_id;
        $this->sort_order = $this->category->sort_order;
        $this->is_active = $this->category->is_active;

        $this->resetValidation();
        $this->modal('edit-category')->show();
    }

    /**
     * 카테고리 수정
     */
    public function update(): void
    {
        if (!$this->category) {
            return;
        }

        // Form Request 클래스에서 validation rules와 messages 가져오기
        $request = new CategoryRequest();
        $validatedData = $this->validate($request->rules(), $request->messages());

        // 카테고리 업데이트 - slug는 CategoryUpdating 이벤트에서 필요시 자동 갱신
        $this->category->update($validatedData);

        // 이벤트 발송
        if ($this->parent_id) {
            $this->dispatch('subcategory-updated');
        } else {
            $this->dispatch('category-updated');
        }

        // 모달 닫기
        $this->dispatch('modal-close', modal: 'edit-category');

        // 폼 초기화
        $this->resetForm();
    }

    /**
     * 폼 리셋
     *
     * @return void
     */
    public function resetForm()
    {
        $this->reset(['name', 'description', 'parent_id', 'sort_order', 'is_active', 'category']);
        $this->resetValidation();
    }

    /**
     * 모달 닫기 시 폼 초기화
     */
    #[On('modal-closed')]
    public function onModalClosed()
    {
        $this->resetForm();
    }
}