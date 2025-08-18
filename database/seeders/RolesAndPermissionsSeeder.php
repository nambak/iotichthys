<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 권한 그룹 정의
        $permissionGroups = [
            'system' => ['manage_users', 'manage_roles', 'manage_organizations'],
            'organization' => ['manage_teams', 'manage_organization_settings', 'invite_members'],
            'team' => ['manage_team_settings', 'manage_team_members'],
            'device' => ['create_device', 'view_device', 'update_device', 'delete_device'],
            'data' => ['view_data', 'export_data', 'create_dashboard', 'edit_dashboard'],
            'alert' => ['create_alert', 'view_alert', 'update_alert', 'delete_alert'],
        ];

        // 권한 생성
        foreach ($permissionGroups as $group => $permissions) {
            foreach ($permissions as $permission) {
                Permission::create([
                    'name' => ucwords(str_replace('_', ' ', $permission)),
                    'slug' => $permission,
                    'group' => $group,
                    'description' => '권한: '.ucwords(str_replace('_', ' ', $permission)),
                ]);
            }
        }

        // 시스템 역할 생성
        $systemRoles = [
            'super_admin' => '모든 권한을 가진 최고 관리자',
            'admin' => '시스템 관리자',
            'user' => '일반 사용자',
        ];

        foreach ($systemRoles as $roleSlug => $description) {
            $role = Role::create([
                'name' => ucwords(str_replace('_', ' ', $roleSlug)),
                'slug' => $roleSlug,
                'description' => $description,
                'is_system_role' => true,
            ]);

            // super_admin 역할에 모든 권한 할당
            if ($roleSlug === 'super_admin') {
                $role->permissions()->attach(Permission::all());
            }

            // admin 역할에 일부 권한 할당
            if ($roleSlug === 'admin') {
                $role->permissions()->attach(
                    Permission::whereIn('group', ['system', 'organization'])->get()
                );
            }

            // user 역할에는 기본 권한만 할당
            if ($roleSlug === 'user') {
                $role->permissions()->attach(
                    Permission::whereIn('slug', ['view_device', 'view_data', 'view_alert'])->get()
                );
            }
        }

        // 조직 특정 역할
        $organizationRoles = [
            'organization_owner' => '조직 소유자',
            'organization_admin' => '조직 관리자',
            'organization_member' => '조직 구성원',
        ];

        foreach ($organizationRoles as $roleSlug => $description) {
            Role::create([
                'name' => ucwords(str_replace('_', ' ', $roleSlug)),
                'slug' => $roleSlug,
                'description' => $description,
                'is_system_role' => false,
            ]);
        }

        // 팀 특정 역할
        $teamRoles = [
            'team_leader' => '팀 리더',
            'team_member' => '팀 구성원',
        ];

        foreach ($teamRoles as $roleSlug => $description) {
            Role::create([
                'name' => ucwords(str_replace('_', ' ', $roleSlug)),
                'slug' => $roleSlug,
                'description' => $description,
                'is_system_role' => false,
            ]);
        }
    }
}
