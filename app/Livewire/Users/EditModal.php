<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class EditModal extends Component
{
    public ?User $user = null;
    public string $name = '';
    public string $email = '';

    public function render()
    {
        return view('livewire.users.edit-modal');
    }

    /**
     * 사용자 편집 모달 열기
     *
     * @param int $userId
     * @return void
     */
    #[On('open-edit-user')]
    public function openEditModal($userId): void
    {
        $this->user = User::withTrashed()->findOrFail($userId);

        // 탈퇴한 사용자는 편집할 수 없음
        if (!$this->user->canBeEdited()) {
            $this->dispatch('show-error-toast', ['message' => '탈퇴한 사용자는 편집할 수 없습니다.']);
            return;
        }

        $this->name = $this->user->name;
        $this->email = $this->user->email;

        $this->modal('edit-user')->show();
    }

    /**
     * 사용자 정보 수정
     */
    public function update(): void
    {
        $validatedData = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->user->id,
        ], [
            'name.required' => '이름을 입력해주세요.',
            'name.max' => '이름은 255자 이하로 입력해주세요.',
            'email.required' => '이메일을 입력해주세요.',
            'email.email' => '올바른 이메일 형식을 입력해주세요.',
            'email.max' => '이메일은 255자 이하로 입력해주세요.',
            'email.unique' => '이미 사용 중인 이메일입니다.',
        ]);

        $this->user->update($validatedData);

        $this->modal('edit-user')->close();

        $this->dispatch('user-updated');

        $this->resetForm();
    }

    /**
     * 폼 리셋
     *
     * @return void
     */
    public function resetForm()
    {
        $this->reset();
        $this->resetValidation();
    }
}
