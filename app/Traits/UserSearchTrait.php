<?php

declare(strict_types=1);

namespace App\Traits;

use App\Exceptions\UserAlreadyMemberException;
use App\Http\Requests\Organization\SearchUserRequest;
use App\Models\User;

trait UserSearchTrait
{
    public string $email = '';
    public ?User $foundUser = null;

    /**
     * 사용자 검색
     */
    public function searchUser(): void
    {
        $request = new SearchUserRequest;
        $this->validate($request->rules(), $request->messages());

        $this->foundUser = User::findByEmail($this->email);

        if (!$this->foundUser) {
            $this->addError('email', '해당 이메일로 가입된 사용자를 찾을 수 없습니다.');

            return;
        }

        // 중복 체크 - 예외 발생 시 호출하는 컴포넌트에서 처리
        $this->validateUserNotDuplicate($this->foundUser);
    }

    /**
     * 사용자가 이미 추가되었는지 확인하고 중복이면 예외 던지기
     *
     */
    protected function validateUserNotDuplicate(User $user): void
    {
        $user->ensureNotMemberOf($this->organization ?? $this->team);
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
        $this->resetErrorBag('email');
    }
}