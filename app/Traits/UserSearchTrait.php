<?php

declare(strict_types=1);

namespace App\Traits;

use App\Http\Requests\Organization\SearchUserRequest;
use App\Models\User;

trait UserSearchTrait
{
    public string $email = '';
    public ?User $foundUser = null;
    public bool $canAddUser = false;

    /**
     * 사용자 검색
     */
    public function searchUser(): void
    {
        $request = new SearchUserRequest;
        $this->validate($request->rules(), $request->messages());

        $this->foundUser = User::where('email', $this->email)->first();
        $this->canAddUser = false;

        if (! $this->foundUser) {
            $this->addError('email', '해당 이메일로 가입된 사용자를 찾을 수 없습니다.');

            return;
        }

        // 중복 체크
        if ($this->isUserAlreadyAdded($this->foundUser)) {
            return;
        }

        // 여기까지 왔다면 추가 가능한 사용자
        $this->canAddUser = true;
    }

    /**
     * 사용자가 이미 추가되었는지 확인
     */
    protected function isUserAlreadyAdded(User $user): bool
    {
        if (isset($this->organization)) {
            if ($this->organization->users()->where('user_id', $user->id)->exists()) {
                $this->addError('email', '해당 사용자는 이미 이 조직에 속해있습니다.');
                return true;
            }
        }

        if (isset($this->team)) {
            if ($this->team->users()->where('user_id', $user->id)->exists()) {
                $this->addError('email', '해당 사용자는 이미 이 팀에 속해있습니다.');
                return true;
            }
        }

        return false;
    }

    /**
     * 조직용 사용자 검색 (하위 호환성)
     */
    public function searchUserForOrganization(): void
    {
        $this->searchUser();
    }

    /**
     * 팀용 사용자 검색 (하위 호환성)
     */
    public function searchUserForTeam(): void
    {
        $this->searchUser();
    }

    /**
     * 검색 결과 초기화
     */
    public function resetSearch(): void
    {
        $this->email = '';
        $this->foundUser = null;
        $this->canAddUser = false;
        $this->resetErrorBag('email');
    }
}