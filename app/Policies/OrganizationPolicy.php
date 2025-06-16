<?php

namespace App\Policies;

use App\Models\User;

class OrganizationPolicy
{
    /**
     * 조직 생성 권한
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        // 조직 생성 권한이 있는 역할을 가진 사용자만 생성 가능
        // 예: 시스템 관리자이거나 조직 생성 권한을 가진 사용자
        return $user->hasPermission('organization.create')
            || $user->hasRole('system-admin');
    }
}
