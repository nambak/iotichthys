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
        ->assertSet('description', '');
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

it('resets form fields after successful creation', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $component = Livewire::test(CreateModal::class)
        ->set('name', 'Device Create')
        ->set('resource', 'device')
        ->set('action', 'create')
        ->set('description', 'Test description')
        ->call('create');

    // All fields should be reset to empty
    $component
        ->assertSet('name', '')
        ->assertSet('resource', '')
        ->assertSet('action', '')
        ->assertSet('description', '');
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