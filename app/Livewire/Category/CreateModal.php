<?php

namespace App\Livewire\Category;

use App\Http\Requests\Category\CategoryRequest;
use App\Models\Category;
use Livewire\Attributes\On;
use Livewire\Component;

class CreateModal extends Component
{
    public $name = '';
    public $description = '';
    public $parent_id = null;
    public $sort_order = 0;
    public $is_active = true;


    public function render()
    {
        return view('livewire.category.create-modal');
    }

    /**
     * 하위 카테고리 생성 모달 열기
     *
     * @param int $parentId
     */
    #[On('open-create-subcategory')]
    public function openForSubcategory($parentId)
    {
        $this->reset();
        $this->parent_id = $parentId;
    }

    /**
     * 카테고리 생성
     */
    public function create()
    {
        // Form Request 클래스에서 validation rules와 messages 가져오기
        $request = new CategoryRequest();
        $validatedData = $this->validate($request->rules(), $request->messages());

        // slug는 CategoryCreating 이벤트에서 GenerateSlug 리스너가 자동 생성
        Category::create($validatedData);

        // 이벤트 발송
        if ($this->parent_id) {
            $this->dispatch('subcategory-created');
        } else {
            $this->dispatch('category-created');
        }

        // 모달 닫기
        $this->dispatch('modal-close', modal: 'create-category');

        // 폼 초기화
        $this->reset();
    }

    /**
     * 모달 닫기 시 폼 초기화
     */
    #[On('modal-closed')]
    public function resetForm()
    {
        $this->reset();
    }
}