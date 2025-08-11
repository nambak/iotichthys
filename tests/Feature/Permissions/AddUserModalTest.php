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
        'slug' => 'device-create'
    ]);

    $this->actingAs($currentUser);

    // Search for user first
    Livewire::test(AddUserModal::class, ['permission' => $permission])
        ->set('email', 'target@example.com')
        ->call('searchUser')
        ->call('addUserToPermission')
        ->assertDispatched('user-added-to-permission');

    // Verify user was added to permission
    expect($permission->name)->toBe('Device Create');
    expect($permission->users)->toHaveCount(1);
    expect($permission->users->first()->id)->toBe($targetUser->id);
    expect($permission->users)->toHaveCount(1);
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