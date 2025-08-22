<?php

namespace App\Livewire\Organization;

use App\Models\Organization;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class UserList extends Component
{
    use WithPagination;

    public $organization;

    public function mount(Organization $organization)
    {
        $this->organization = $organization;
    }

    public function render()
    {
        $users = $this->organization->users()->paginate(10);

        return view('livewire.organization.user-list', compact('users'));
    }

    /**
     * 조직에서 사용자 제거
     */
    public function removeUserFromOrganization($userId)
    {
        $user = User::findOrFail($userId);

        // 조직 소유자는 제거할 수 없음
        $userOrganization = $this->organization->users()->where('user_id', $userId)->first();
        if ($userOrganization && $userOrganization->pivot->is_owner) {
            $this->dispatch('show-error-toast', [
                'message' => '조직 소유자는 제거할 수 없습니다.',
            ]);

            return;
        }

        $this->organization->users()->detach($userId);

        $this->dispatch('user-removed-from-organization', [
            'message' => $user->name . '님이 조직에서 제거되었습니다.',
        ]);

        $this->resetPage();
    }

    #[On('user-added-to-organization')]
    public function refreshAfterCreate()
    {
        $this->resetPage();
    }
}
