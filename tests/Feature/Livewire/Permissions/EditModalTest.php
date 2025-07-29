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
        'slug' => 'device_create',
        'resource' => 'device',
        'action' => 'create',
        'description' => 'Permission to create devices'
    ]);

    Livewire::test(EditModal::class)
        ->dispatch('open-edit-permission', permissionId: $permission->id)
        ->assertSet('permission.id', $permission->id)
        ->assertSet('name', 'Device Create')
        ->assertSet('slug', 'device_create')
        ->assertSet('resource', 'device')
        ->assertSet('action', 'create')
        ->assertSet('description', 'Permission to create devices')
        ->assertDispatched('modal-open', name: 'edit-permission');
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
        ->set('slug', 'device_manage')
        ->set('resource', 'device')
        ->set('action', 'manage')
        ->set('description', 'Updated description')
        ->call('update')
        ->assertHasNoErrors()
        ->assertDispatched('permission-updated')
        ->assertDispatched('modal-close', name: 'edit-permission');

    $permission->refresh();
    expect($permission->name)->toBe('Device Manage');
    expect($permission->slug)->toBe('device_manage');
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
        ->set('slug', '')
        ->set('resource', '')
        ->set('action', '')
        ->call('update')
        ->assertHasErrors([
            'name' => 'required',
            'slug' => 'required',
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
        ->set('slug', $longString)
        ->set('resource', $longString)
        ->set('action', $longString)
        ->call('update')
        ->assertHasErrors([
            'name' => 'max',
            'slug' => 'max',
            'resource' => 'max',
            'action' => 'max'
        ]);
});

it('validates slug uniqueness excluding current permission', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $permission1 = Permission::factory()->create(['slug' => 'device_create']);
    $permission2 = Permission::factory()->create(['slug' => 'device_update']);

    // Should allow keeping the same slug
    Livewire::test(EditModal::class)
        ->dispatch('open-edit-permission', permissionId: $permission1->id)
        ->set('name', 'Device Create Updated')
        ->set('slug', 'device_create')
        ->set('resource', 'device')
        ->set('action', 'create')
        ->call('update')
        ->assertHasNoErrors();

    // Should not allow using another permission's slug
    Livewire::test(EditModal::class)
        ->dispatch('open-edit-permission', permissionId: $permission1->id)
        ->set('name', 'Device Create')
        ->set('slug', 'device_update')
        ->set('resource', 'device')
        ->set('action', 'create')
        ->call('update')
        ->assertHasErrors(['slug' => 'unique']);
});

it('auto-generates slug from name on update', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $permission = Permission::factory()->create();

    Livewire::test(EditModal::class)
        ->dispatch('open-edit-permission', permissionId: $permission->id)
        ->set('name', 'Device Manage Permission')
        ->assertSet('slug', 'device_manage_permission');
});

it('resets form fields after successful update', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $permission = Permission::factory()->create();

    $component = Livewire::test(EditModal::class)
        ->dispatch('open-edit-permission', permissionId: $permission->id)
        ->set('name', 'Updated Name')
        ->set('slug', 'updated_slug')
        ->set('resource', 'updated')
        ->set('action', 'update')
        ->set('description', 'Updated description')
        ->call('update');

    // All fields should be reset to empty
    $component
        ->assertSet('name', '')
        ->assertSet('slug', '')
        ->assertSet('resource', '')
        ->assertSet('action', '')
        ->assertSet('description', '')
        ->assertSet('permission', null);
});

it('can cancel and reset form', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $permission = Permission::factory()->create([
        'name' => 'Original Name',
        'slug' => 'original_slug'
    ]);

    $component = Livewire::test(EditModal::class)
        ->dispatch('open-edit-permission', permissionId: $permission->id)
        ->set('name', 'Changed Name')
        ->set('slug', 'changed_slug')
        ->set('resource', 'changed')
        ->set('action', 'change')
        ->set('description', 'Changed description')
        ->call('cancel');

    // All fields should be reset to empty
    $component
        ->assertSet('name', '')
        ->assertSet('slug', '')
        ->assertSet('resource', '')
        ->assertSet('action', '')
        ->assertSet('description', '')
        ->assertSet('permission', null)
        ->assertDispatched('modal-close', name: 'edit-permission');

    // Original permission should remain unchanged
    $permission->refresh();
    expect($permission->name)->toBe('Original Name');
    expect($permission->slug)->toBe('original_slug');
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
        ->set('slug', 'updated_slug')
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
        ->assertSet('slug', 'device_update');
});