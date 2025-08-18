<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    /**
     * 조직 생성 권한
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('organization.create');
    }

    /**
     * 조직 수정 권한
     */
    public function update(User $user, Organization $organization): bool
    {
        // TODO: 조직 소유자 또는 관리자 권한 체크 로직 구현
        return $user->hasPermission('organization.update');
    }

    /**
     * 조직 삭제 권한
     */
    public function delete(User $user, Organization $organization): bool
    {
        // TODO: 조직 소유자 또는 관리자 권한 체크 로직 구현
        // TODO: 조직에 연결된 팀이나 사용자가 있는지 확인
        return $user->hasPermission('organization.delete');
    }
}
