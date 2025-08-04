<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        $users = User::withTrashed()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.users.index', compact('users'));
    }

    /**
     * 사용자 탈퇴 처리
     *
     * @param int $userId
     * @return void
     */
    public function withdraw($userId): void
    {
        $user = User::withTrashed()->findOrFail($userId);

        // 자기 자신은 탈퇴할 수 없음
        if ($user->id === auth()->id()) {
            $this->dispatch('show-error-toast', ['message' => '자기 자신은 탈퇴시킬 수 없습니다.']);
            return;
        }

        // 이미 탈퇴한 사용자는 탈퇴할 수 없음
        if ($user->isWithdrawn()) {
            $this->dispatch('show-error-toast', ['message' => '이미 탈퇴한 사용자입니다.']);
            return;
        }

        $user->withdraw();

        $this->dispatch('user-withdrawn');
        $this->resetPage();
    }

    /**
     * 사용자 편집 모달 열기
     *
     * @param User $user
     * @return void
     */
    public function edit(User $user): void
    {
        // 탈퇴한 사용자는 편집할 수 없음
        if (!$user->canBeEdited()) {
            $this->dispatch('show-error-toast', ['message' => '탈퇴한 사용자는 편집할 수 없습니다.']);
            return;
        }

        $this->dispatch('open-edit-user', userId: $user->id);
    }

    /**
     * 사용자 생성, 수정 성공 시 처리
     */
    #[On('user-updated')]
    public function refreshAfterUpdate()
    {
        $this->resetPage();
    }
}