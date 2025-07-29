<?php

use App\Livewire\Permissions\CreateModal;
use App\Models\Permission;
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
        ->assertSet('slug', '')
        ->assertSet('resource', '')
        ->assertSet('action', '')
        ->assertSet('description', '');
});

it('can create permission with valid data', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    expect(Permission::count())->toBe(0);

    Livewire::test(CreateModal::class)
        ->set('name', 'Device Create')
        ->set('slug', 'device_create')
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
    expect($permission->slug)->toBe('device_create');
    expect($permission->resource)->toBe('device');
    expect($permission->action)->toBe('create');
    expect($permission->description)->toBe('Permission to create devices');
});

it('validates required fields', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(CreateModal::class)
        ->call('create')
        ->assertHasErrors([
            'name' => 'required',
            'slug' => 'required',
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
        ->set('slug', $longString)
        ->set('resource', $longString)
        ->set('action', $longString)
        ->call('create')
        ->assertHasErrors([
            'name' => 'max',
            'slug' => 'max',
            'resource' => 'max',
            'action' => 'max'
        ]);

    expect(Permission::count())->toBe(0);
});

it('validates slug uniqueness', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Permission::factory()->create(['slug' => 'device_create']);

    Livewire::test(CreateModal::class)
        ->set('name', 'Device Create')
        ->set('slug', 'device_create')
        ->set('resource', 'device')
        ->set('action', 'create')
        ->call('create')
        ->assertHasErrors(['slug' => 'unique']);

    expect(Permission::count())->toBe(1);
});

it('auto-generates slug from name', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(CreateModal::class)
        ->set('name', 'Device Create Permission')
        ->assertSet('slug', 'device_create_permission');
});

it('handles name with special characters for slug generation', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(CreateModal::class)
        ->set('name', 'Device-Create & Update!')
        ->assertSet('slug', 'device-create_&_update!');
});

it('handles empty description field', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(CreateModal::class)
        ->set('name', 'Device Create')
        ->set('slug', 'device_create')
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
    $this->actingAs($user);

    $component = Livewire::test(CreateModal::class)
        ->set('name', 'Device Create')
        ->set('slug', 'device_create')
        ->set('resource', 'device')
        ->set('action', 'create')
        ->set('description', 'Test description')
        ->call('create');

    // All fields should be reset to empty
    $component
        ->assertSet('name', '')
        ->assertSet('slug', '')
        ->assertSet('resource', '')
        ->assertSet('action', '')
        ->assertSet('description', '');
});

it('can cancel and reset form', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $component = Livewire::test(CreateModal::class)
        ->set('name', 'Device Create')
        ->set('slug', 'device_create')
        ->set('resource', 'device')
        ->set('action', 'create')
        ->set('description', 'Test description')
        ->call('cancel');

    // All fields should be reset to empty
    $component
        ->assertSet('name', '')
        ->assertSet('slug', '')
        ->assertSet('resource', '')
        ->assertSet('action', '')
        ->assertSet('description', '')
        ->assertDispatched('modal-close', name: 'create-permission');

    expect(Permission::count())->toBe(0);
});

it('trims whitespace from name before generating slug', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(CreateModal::class)
        ->set('name', '  Device Create  ')
        ->assertSet('slug', 'device_create');
});

it('handles multiple spaces in name for slug generation', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(CreateModal::class)
        ->set('name', 'Device    Create    Permission')
        ->assertSet('slug', 'device____create____permission');
});