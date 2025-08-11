<?php

namespace App\Livewire\Category;

use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class CreateModal extends Component
{
    public $name = '';
    public $description = '';
    public $parent_id = null;
    public $sort_order = 0;
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'parent_id' => 'nullable|exists:categories,id',
        'sort_order' => 'integer|min:0',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'name.required' => '카테고리 이름을 입력해주세요.',
        'name.max' => '카테고리 이름은 255자를 초과할 수 없습니다.',
        'description.max' => '설명은 1000자를 초과할 수 없습니다.',
        'parent_id.exists' => '유효하지 않은 상위 카테고리입니다.',
        'sort_order.integer' => '정렬 순서는 숫자여야 합니다.',
        'sort_order.min' => '정렬 순서는 0 이상이어야 합니다.',
    ];

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
        $this->validate();

        // Slug 생성
        $slug = Str::slug($this->name);
        $originalSlug = $slug;
        $counter = 1;

        // 중복되지 않는 slug 생성
        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $category = Category::create([
            'name' => $this->name,
            'slug' => $slug,
            'description' => $this->description,
            'parent_id' => $this->parent_id,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
        ]);

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