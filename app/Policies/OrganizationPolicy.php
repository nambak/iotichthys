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
        return $user->hasPermission('organization.create');
    }
}
