<?php

namespace App\Livewire\Category;

use App\Models\Category;
use App\Models\CategoryAccessControl;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryPermissions extends Component
{
    use WithPagination;

    public Category $category;
    public $searchTerm = '';
    
    public function mount(Category $category)
    {
        $this->category = $category;
    }

    public function render()
    {
        // 권한이 있는 사용자들
        $authorizedUsers = $this->category->getAuthorizedUsers();
        
        // 권한이 없는 사용자들 (검색어 적용)
        $availableUsers = User::whereDoesntHave('categoryAccessControls', function ($query) {
            $query->where('category_id', $this->category->id);
        })
        ->when($this->searchTerm, function ($query) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $this->searchTerm . '%');
            });
        })
        ->paginate(5);

        return view('livewire.category.category-permissions', [
            'authorizedUsers' => $authorizedUsers,
            'availableUsers' => $availableUsers,
        ]);
    }

    /**
     * 사용자에게 카테고리 권한 부여
     */
    public function grantAccess($userId)
    {
        $user = User::findOrFail($userId);
        
        // 이미 권한이 있는지 확인
        $exists = CategoryAccessControl::where('user_id', $userId)
            ->where('category_id', $this->category->id)
            ->exists();
            
        if (!$exists) {
            CategoryAccessControl::create([
                'user_id' => $userId,
                'category_id' => $this->category->id,
            ]);
            
            $this->dispatch('show-success-toast', [
                'message' => "{$user->name}님에게 '{$this->category->name}' 카테고리 권한을 부여했습니다."
            ]);
        }
        
        $this->resetPage();
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
            'message' => "{$user->name}님의 '{$this->category->name}' 카테고리 권한을 취소했습니다."
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
