<?php

use App\Livewire\Permissions\EditModal;
use App\Models\Permission;
use App\Models\User;
use Livewire\Livewire;

it('can render edit modal', function () {
    $user = User::factory()->create(); 
    $this->actingAs($user);

    Livewire::test(EditModal::class)
        ->assertOk()
        ->assertViewIs('livewire.permissions.edit-modal');
});

it('has empty form fields by default', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(EditModal::class)
        ->assertSet('name', '')
        ->assertSet('slug', '')
        ->assertSet('resource', '')
        ->assertSet('action', '')
        ->assertSet('description', '')
        ->assertSet('permission', null);
});

it('loads permission data when modal is opened', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $permission = Permission::factory()->create([
        'name' => 'Device Create',
        'resource' => 'device',
        'action' => 'create',
        'description' => 'Permission to create devices'
    ]);

    Livewire::test(EditModal::class)
        ->dispatch('open-edit-permission', permissionId: $permission->id)
        ->assertSet('permission.id', $permission->id)
        ->assertSet('name', 'Device Create')
        ->assertSet('resource', 'device')
        ->assertSet('action', 'create')
        ->assertSet('description', 'Permission to create devices');
});

it('loads permission data with null description', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $permission = Permission::factory()->create([
        'name' => 'Device Create',
        'description' => null
    ]);

    Livewire::test(EditModal::class)
        ->dispatch('open-edit-permission', permissionId: $permission->id)
        ->assertSet('description', '');
});

it('can update permission with valid data', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $permission = Permission::factory()->create([
        'name' => 'Device Create',
        'slug' => 'device_create',
        'resource' => 'device',
        'action' => 'create',
        'description' => 'Old description'
    ]);

    Livewire::test(EditModal::class)
        ->dispatch('open-edit-permission', permissionId: $permission->id)
        ->set('name', 'Device Manage')
        ->set('resource', 'device')
        ->set('action', 'manage')
        ->set('description', 'Updated description')
        ->call('update')
        ->assertHasNoErrors()
        ->assertDispatched('permission-updated')
        ->assertDispatched('modal-close', name: 'edit-permission');

    $permission->refresh();
    expect($permission->name)->toBe('Device Manage');
    expect($permission->resource)->toBe('device');
    expect($permission->action)->toBe('manage');
    expect($permission->description)->toBe('Updated description');
});

it('validates required fields on update', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $permission = Permission::factory()->create();

    Livewire::test(EditModal::class)
        ->dispatch('open-edit-permission', permissionId: $permission->id)
        ->set('name', '')
        ->set('resource', '')
        ->set('action', '')
        ->call('update')
        ->assertHasErrors([
            'name' => 'required',
            'resource' => 'required',
            'action' => 'required'
        ]);
});

it('validates maximum field lengths on update', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $permission = Permission::factory()->create();
    $longString = str_repeat('a', 256);

    Livewire::test(EditModal::class)
        ->dispatch('open-edit-permission', permissionId: $permission->id)
        ->set('name', $longString)
        ->set('resource', $longString)
        ->set('action', $longString)
        ->call('update')
        ->assertHasErrors([
            'name' => 'max',
            'resource' => 'max',
            'action' => 'max'
        ]);
});

it('auto-generates slug from name on update', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $permission = Permission::factory()->create();

    dump($permission);

    Livewire::test(EditModal::class)
        ->dispatch('open-edit-permission', permissionId: $permission->id)
        ->set('name', 'Device Manage Permission')
        ->call('update');

    $permission->refresh();

    dump($permission);

    expect($permission->slug)->toBe('device-manage-permission');
});

it('resets form fields after successful update', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $permission = Permission::factory()->create();

    $component = Livewire::test(EditModal::class)
        ->dispatch('open-edit-permission', permissionId: $permission->id)
        ->set('name', 'Updated Name')
        ->set('resource', 'updated')
        ->set('action', 'update')
        ->set('description', 'Updated description')
        ->call('update');

    // All fields should be reset to empty
    $component
        ->assertSet('name', '')
        ->assertSet('resource', '')
        ->assertSet('action', '')
        ->assertSet('description', '')
        ->assertSet('permission', null);
});

it('throws exception when trying to load non-existent permission', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(EditModal::class)
        ->dispatch('open-edit-permission', permissionId: 99999)
        ->assertHasErrors();
})->throws('Illuminate\Database\Eloquent\ModelNotFoundException');

it('handles updating permission with empty description', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $permission = Permission::factory()->create([
        'description' => 'Original description'
    ]);

    Livewire::test(EditModal::class)
        ->dispatch('open-edit-permission', permissionId: $permission->id)
        ->set('name', 'Updated Name')
        ->set('resource', 'device')
        ->set('action', 'create')
        ->set('description', '')
        ->call('update')
        ->assertHasNoErrors();

    $permission->refresh();

    expect($permission->description)->toBe('');
});

it('trims whitespace from name before generating slug on update', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $permission = Permission::factory()->create();

    Livewire::test(EditModal::class)
        ->dispatch('open-edit-permission', permissionId: $permission->id)
        ->set('name', '  Device Update  ')
        ->call('update');

    $permission->refresh();
    expect($permission->slug)->toBe('device-update');
});