<?php

use App\Livewire\Permissions\Show;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;

it('can render permission show page', function () {
    $user = User::factory()->create();
    $permission = Permission::factory()->create();
    $this->actingAs($user);

    Livewire::test(Show::class, ['permission' => $permission])
        ->assertOk()
        ->assertViewIs('livewire.permissions.show');
});

it('displays permission details correctly', function () {
    $user = User::factory()->create();
    $permission = Permission::factory()->create([
        'name' => 'Device Create',
        'resource' => 'device',
        'action' => 'create',
        'description' => 'Permission to create devices',
        'slug' => 'device_create'
    ]);
    $this->actingAs($user);

    Livewire::test(Show::class, ['permission' => $permission])
        ->assertSee('Device Create')
        ->assertSee('device')
        ->assertSee('create')
        ->assertSee('Permission to create devices')
        ->assertSee('device_create');
});

it('displays users who have the permission', function () {
    $currentUser = User::factory()->create();
    $user1 = User::factory()->create(['name' => 'User One', 'email' => 'user1@example.com']);
    $user2 = User::factory()->create(['name' => 'User Two', 'email' => 'user2@example.com']);
    
    $permission = Permission::factory()->create();
    $role = Role::factory()->create();
    
    // Assign permission to role
    $role->permissions()->attach($permission->id);
    
    // Assign users to role
    $user1->roles()->attach($role->id);
    $user2->roles()->attach($role->id);
    
    $this->actingAs($currentUser);

    Livewire::test(Show::class, ['permission' => $permission])
        ->assertSee('User One')
        ->assertSee('user1@example.com')
        ->assertSee('User Two')
        ->assertSee('user2@example.com');
});

it('can remove user from permission', function () {
    $currentUser = User::factory()->create();
    $userToRemove = User::factory()->create();
    
    $permission = Permission::factory()->create();
    $role = Role::factory()->create();
    
    // Assign permission to role and user to role
    $role->permissions()->attach($permission->id);
    $userToRemove->roles()->attach($role->id);
    
    $this->actingAs($currentUser);

    // Verify user has the permission initially
    expect($userToRemove->roles()->whereHas('permissions', function ($query) use ($permission) {
        $query->where('permission_id', $permission->id);
    })->exists())->toBeTrue();

    Livewire::test(Show::class, ['permission' => $permission])
        ->call('removeUserFromPermission', $userToRemove->id)
        ->assertDispatched('user-removed-from-permission');

    // Verify user no longer has the permission
    expect($userToRemove->fresh()->roles()->whereHas('permissions', function ($query) use ($permission) {
        $query->where('permission_id', $permission->id);
    })->exists())->toBeFalse();
});

it('handles empty user list gracefully', function () {
    $user = User::factory()->create();
    $permission = Permission::factory()->create();
    $this->actingAs($user);

    Livewire::test(Show::class, ['permission' => $permission])
        ->assertSee('사용자 없음')
        ->assertSee('이 권한을 가진 사용자가 없습니다');
});

it('displays correct user count', function () {
    $currentUser = User::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    
    $permission = Permission::factory()->create();
    $role = Role::factory()->create();
    
    // Assign permission to role
    $role->permissions()->attach($permission->id);
    
    // Assign users to role
    $user1->roles()->attach($role->id);
    $user2->roles()->attach($role->id);
    
    $this->actingAs($currentUser);

    Livewire::test(Show::class, ['permission' => $permission])
        ->assertSee('권한을 가진 사용자 (2명)');
});