<?php

namespace App\Livewire\Category;

use App\Models\Category;
use App\Models\CategoryAccessControl;
use App\Models\User;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

class AddUserModal extends Component
{
    use WithPagination;

    public ?User $foundUser = null;

    public string $email = '';

    public Category $category;

    public function mount(Category $category): void
    {
        $this->category = $category;
    }

    public function render()
    {
        return view('livewire.category.add-user-modal');
    }

    /**
     * 사용자 검색
     */
    public function searchUser(): void
    {
        $this->validate([
            'email' => 'required|email',
        ], [
            'email.required' => '이메일을 입력해주세요.',
            'email.email' => '올바른 이메일 주소를 입력해주세요.',
        ]);

        $this->foundUser = User::where('email', $this->email)->first();

        if (! $this->foundUser) {
            $this->addError('email', '해당 이메일로 가입된 사용자를 찾을 수 없습니다.');

            return;
        }

        // 이미 권한이 있는 사용자인지 확인
        $exists = CategoryAccessControl::where('user_id', $this->foundUser->id)
            ->where('category_id', $this->category->id)
            ->exists();

        if ($exists) {
            $this->addError('email', '해당 사용자는 이미 이 카테고리에 권한이 있습니다.');
        }
    }

    /**
     * 사용자에게 카테고리 권한 부여
     */
    public function grantAccess(): void
    {
        if (! $this->foundUser) {
            $this->addError('email', '먼저 사용자를 검색해주세요.');

            return;
        }

        CategoryAccessControl::create([
            'user_id' => $this->foundUser->id,
            'category_id' => $this->category->id,
        ]);

        $this->dispatch('show-success-toast', [
            'message' => "{$this->foundUser->name}님에게 '{$this->category->name}' 카테고리 권한을 부여했습니다.",
        ]);

        Flux::modals()->close('add-permission-user-modal');

        $this->dispatch('category-permissions-updated');
    }

    /**
     * 모달 닫기 시 폼 초기화
     */
    public function resetForm()
    {
        $this->email = '';
        $this->foundUser = null;
        $this->resetValidation();
        $this->reset();
    }
}
