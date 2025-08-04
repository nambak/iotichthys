<?php

use App\Livewire\Permissions\CreateModal;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;

it('can render create modal', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(CreateModal::class)
        ->assertOk()
        ->assertViewIs('livewire.permissions.create-modal');
});

it('has empty form fields by default', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(CreateModal::class)
        ->assertSet('name', '')
        ->assertSet('resource', '')
        ->assertSet('action', '')
        ->assertSet('description', '')
        ->assertSet('selectedUsers', []);
});

it('can create permission with valid data', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    expect(Permission::count())->toBe(0);

    Livewire::test(CreateModal::class)
        ->set('name', 'Device Create')
        ->set('resource', 'device')
        ->set('action', 'create')
        ->set('description', 'Permission to create devices')
        ->call('create')
        ->assertHasNoErrors()
        ->assertDispatched('permission-created')
        ->assertDispatched('modal-close', name: 'create-permission');

    expect(Permission::count())->toBe(1);
    
    $permission = Permission::first();
    expect($permission->name)->toBe('Device Create');
    expect($permission->resource)->toBe('device');
    expect($permission->action)->toBe('create');
    expect($permission->description)->toBe('Permission to create devices');
    expect($permission->slug)->toBe('device_create');
});

it('can create permission with assigned users', function () {
    $currentUser = User::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $this->actingAs($currentUser);

    expect(Permission::count())->toBe(0);

    Livewire::test(CreateModal::class)
        ->set('name', 'Device Create')
        ->set('resource', 'device')
        ->set('action', 'create')
        ->set('description', 'Permission to create devices')
        ->set('selectedUsers', [$user1->id, $user2->id])
        ->call('create')
        ->assertHasNoErrors()
        ->assertDispatched('permission-created');

    expect(Permission::count())->toBe(1);
    
    $permission = Permission::first();
    expect($permission->name)->toBe('Device Create');
    expect($permission->slug)->toBe('device_create');

    // Check that a role was created for this permission
    $role = $permission->roles()->first();
    expect($role)->not->toBeNull();
    expect($role->name)->toBe('Device Create Role');
    expect($role->slug)->toBe('device_create_role');
    expect($role->is_system_role)->toBeFalse();

    // Check that users were assigned to the role
    expect($role->users)->toHaveCount(2);
    expect($role->users->pluck('id')->toArray())->toContain($user1->id, $user2->id);

    // Verify users have the permission through the role
    expect($user1->fresh()->hasPermission('device_create'))->toBeTrue();
    expect($user2->fresh()->hasPermission('device_create'))->toBeTrue();
});

it('validates required fields', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(CreateModal::class)
        ->call('create')
        ->assertHasErrors([
            'name' => 'required',
            'resource' => 'required',
            'action' => 'required'
        ]);

    expect(Permission::count())->toBe(0);
});

it('validates maximum field lengths', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $longString = str_repeat('a', 256);

    Livewire::test(CreateModal::class)
        ->set('name', $longString)
        ->set('resource', $longString)
        ->set('action', $longString)
        ->call('create')
        ->assertHasErrors([
            'name' => 'max',
            'resource' => 'max',
            'action' => 'max'
        ]);

    expect(Permission::count())->toBe(0);
});

it('handles empty description field', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(CreateModal::class)
        ->set('name', 'Device Create')
        ->set('resource', 'device')
        ->set('action', 'create')
        ->set('description', '')
        ->call('create')
        ->assertHasNoErrors();

    expect(Permission::count())->toBe(1);
    expect(Permission::first()->description)->toBe('');
});

it('resets form fields after successful creation', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $this->actingAs($user);

    $component = Livewire::test(CreateModal::class)
        ->set('name', 'Device Create')
        ->set('resource', 'device')
        ->set('action', 'create')
        ->set('description', 'Test description')
        ->set('selectedUsers', [$otherUser->id])
        ->call('create');

    // All fields should be reset to empty
    $component
        ->assertSet('name', '')
        ->assertSet('resource', '')
        ->assertSet('action', '')
        ->assertSet('description', '')
        ->assertSet('selectedUsers', []);
});


it('권한 이름이 비어있으면 에러 발생', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(CreateModal::class)
        ->set('name', '')
        ->set('resource', 'device')
        ->set('action', 'create')
        ->set('description', 'Test description')
        ->call('create')
        ->assertHasErrors(['name' => 'required'])
        ->assertSeeHtml('<div role="alert"')
        ->assertSee('권한 이름을 입력해 주세요', false);

});

it('displays users for selection excluding current user', function () {
    $currentUser = User::factory()->create(['name' => 'Current User']);
    $user1 = User::factory()->create(['name' => 'User One']);
    $user2 = User::factory()->create(['name' => 'User Two']);
    $this->actingAs($currentUser);

    $component = Livewire::test(CreateModal::class);
    
    $users = $component->viewData('users');
    expect($users)->toHaveCount(2);
    expect($users->pluck('name')->toArray())->toContain('User One', 'User Two');
    expect($users->pluck('name')->toArray())->not->toContain('Current User');
});

it('handles empty user selection gracefully', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(CreateModal::class)
        ->set('name', 'Device Create')
        ->set('resource', 'device')
        ->set('action', 'create')
        ->set('selectedUsers', [])
        ->call('create')
        ->assertHasNoErrors();

    $permission = Permission::first();
    expect($permission)->not->toBeNull();
    expect($permission->roles)->toHaveCount(0);
});