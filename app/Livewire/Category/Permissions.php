<?php

namespace App\Livewire\Category;

use App\Models\Category;
use App\Models\CategoryAccessControl;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Permissions extends Component
{
    use WithPagination;

    public Category $category;

    public function mount(Category $category): void
    {
        $this->category = $category;
    }

    public function render()
    {
        // 권한이 있는 사용자들
        $authorizedUsers = $this->category->getAuthorizedUsers();

        return view('livewire.category.permissions', compact('authorizedUsers'));
    }

    /**
     * 사용자의 카테고리 권한 취소
     */
    public function revokeAccess($userId)
    {
        $user = User::findOrFail($userId);

        CategoryAccessControl::where('user_id', $userId)
            ->where('category_id', $this->category->id)
            ->delete();

        $this->dispatch('show-success-toast', [
            'message' => "{$user->name}님의 '{$this->category->name}' 카테고리 권한을 취소했습니다.",
        ]);

        $this->resetPage();
    }

    /**
     * 검색어 업데이트 시 페이지 리셋
     */
    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    /**
     * 권한 변경 시 새로고침
     */
    #[On('category-permissions-updated')]
    public function refreshPermissions()
    {
        $this->resetPage();
    }
}
