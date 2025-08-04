<?php

use App\Livewire\Permissions\AddUserModal;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;

it('can render add user modal', function () {
    $user = User::factory()->create();
    $permission = Permission::factory()->create();
    $this->actingAs($user);

    Livewire::test(AddUserModal::class, ['permission' => $permission])
        ->assertOk()
        ->assertViewIs('livewire.permissions.add-user-modal');
});

it('can search for user by email', function () {
    $currentUser = User::factory()->create();
    $targetUser = User::factory()->create([
        'name' => 'Target User',
        'email' => 'target@example.com'
    ]);
    $permission = Permission::factory()->create();
    $this->actingAs($currentUser);

    Livewire::test(AddUserModal::class, ['permission' => $permission])
        ->set('email', 'target@example.com')
        ->call('searchUser')
        ->assertHasNoErrors()
        ->assertSet('foundUser.id', $targetUser->id)
        ->assertSet('canAddUser', true)
        ->assertSee('Target User')
        ->assertSee('target@example.com');
});

it('shows error when user not found', function () {
    $user = User::factory()->create();
    $permission = Permission::factory()->create();
    $this->actingAs($user);

    Livewire::test(AddUserModal::class, ['permission' => $permission])
        ->set('email', 'nonexistent@example.com')
        ->call('searchUser')
        ->assertHasErrors(['email'])
        ->assertSet('foundUser', null)
        ->assertSet('canAddUser', false);
});

it('shows error when user already has permission', function () {
    $currentUser = User::factory()->create();
    $targetUser = User::factory()->create(['email' => 'target@example.com']);
    $permission = Permission::factory()->create();
    $role = Role::factory()->create();
    
    // Give user the permission through a role
    $role->permissions()->attach($permission->id);
    $targetUser->roles()->attach($role->id);
    
    $this->actingAs($currentUser);

    Livewire::test(AddUserModal::class, ['permission' => $permission])
        ->set('email', 'target@example.com')
        ->call('searchUser')
        ->assertHasErrors(['email'])
        ->assertSet('canAddUser', false);
});

it('can add user to permission', function () {
    $currentUser = User::factory()->create();
    $targetUser = User::factory()->create(['email' => 'target@example.com']);
    $permission = Permission::factory()->create([
        'name' => 'Device Create',
        'slug' => 'device_create'
    ]);
    $this->actingAs($currentUser);

    // Search for user first
    $component = Livewire::test(AddUserModal::class, ['permission' => $permission])
        ->set('email', 'target@example.com')
        ->call('searchUser')
        ->assertSet('canAddUser', true);

    // Add user to permission
    $component->call('addUserToPermission')
        ->assertDispatched('user-added-to-permission');

    // Verify user was added to permission through role
    $role = Role::where('slug', 'device_create_role')->first();
    expect($role)->not->toBeNull();
    expect($role->name)->toBe('Device Create Role');
    expect($role->users)->toHaveCount(1);
    expect($role->users->first()->id)->toBe($targetUser->id);
    expect($role->permissions)->toHaveCount(1);
    expect($role->permissions->first()->id)->toBe($permission->id);
});

it('creates role if it does not exist when adding user', function () {
    $currentUser = User::factory()->create();
    $targetUser = User::factory()->create(['email' => 'target@example.com']);
    $permission = Permission::factory()->create([
        'name' => 'New Permission',
        'slug' => 'new_permission'
    ]);
    $this->actingAs($currentUser);

    expect(Role::where('slug', 'new_permission_role')->exists())->toBeFalse();

    Livewire::test(AddUserModal::class, ['permission' => $permission])
        ->set('email', 'target@example.com')
        ->call('searchUser')
        ->call('addUserToPermission');

    $role = Role::where('slug', 'new_permission_role')->first();
    expect($role)->not->toBeNull();
    expect($role->name)->toBe('New Permission Role');
    expect($role->description)->toBe('권한 "New Permission"을 위해 자동 생성된 역할');
    expect($role->is_system_role)->toBeFalse();
});

it('uses existing role if it exists when adding user', function () {
    $currentUser = User::factory()->create();
    $targetUser = User::factory()->create(['email' => 'target@example.com']);
    $permission = Permission::factory()->create([
        'name' => 'Existing Permission',
        'slug' => 'existing_permission'
    ]);
    
    // Create role beforehand
    $existingRole = Role::factory()->create([
        'slug' => 'existing_permission_role',
        'name' => 'Existing Permission Role'
    ]);
    $existingRole->permissions()->attach($permission->id);
    
    $this->actingAs($currentUser);

    $roleCountBefore = Role::count();

    Livewire::test(AddUserModal::class, ['permission' => $permission])
        ->set('email', 'target@example.com')
        ->call('searchUser')
        ->call('addUserToPermission');

    // Should not create a new role
    expect(Role::count())->toBe($roleCountBefore);
    
    // Should use existing role
    expect($existingRole->fresh()->users)->toHaveCount(1);
    expect($existingRole->fresh()->users->first()->id)->toBe($targetUser->id);
});

it('resets form after successful user addition', function () {
    $currentUser = User::factory()->create();
    $targetUser = User::factory()->create(['email' => 'target@example.com']);
    $permission = Permission::factory()->create();
    $this->actingAs($currentUser);

    $component = Livewire::test(AddUserModal::class, ['permission' => $permission])
        ->set('email', 'target@example.com')
        ->call('searchUser')
        ->call('addUserToPermission');

    // Form should be reset
    $component
        ->assertSet('email', '')
        ->assertSet('foundUser', null)
        ->assertSet('canAddUser', false);
});

it('validates email format', function () {
    $user = User::factory()->create();
    $permission = Permission::factory()->create();
    $this->actingAs($user);

    Livewire::test(AddUserModal::class, ['permission' => $permission])
        ->set('email', 'invalid-email')
        ->call('searchUser')
        ->assertHasErrors(['email']);
});

it('requires email to be provided', function () {
    $user = User::factory()->create();
    $permission = Permission::factory()->create();
    $this->actingAs($user);

    Livewire::test(AddUserModal::class, ['permission' => $permission])
        ->set('email', '')
        ->call('searchUser')
        ->assertHasErrors(['email']);
});