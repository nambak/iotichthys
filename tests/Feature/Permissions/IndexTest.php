<?php

use App\Livewire\Permissions\Index;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;

it('can render permissions index page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Permission::factory()->count(5)->create();

    Livewire::test(Index::class)
        ->assertOk()
        ->assertViewIs('livewire.permissions.index');
});

it('displays permissions ordered by resource and action', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create permissions in different order
    $permission1 = Permission::factory()->withResourceAction('user', 'delete')->create();
    $permission2 = Permission::factory()->withResourceAction('device', 'create')->create();
    $permission3 = Permission::factory()->withResourceAction('device', 'read')->create();
    $permission4 = Permission::factory()->withResourceAction('user', 'create')->create();

    $component = Livewire::test(Index::class);

    // Should be ordered by resource first, then action
    $permissions = $component->viewData('permissions');
    
    expect($permissions->items())->toHaveCount(4);
    expect($permissions->items()[0]->resource)->toBe('device');
    expect($permissions->items()[0]->action)->toBe('create');
    expect($permissions->items()[1]->resource)->toBe('device');
    expect($permissions->items()[1]->action)->toBe('read');
    expect($permissions->items()[2]->resource)->toBe('user');
    expect($permissions->items()[2]->action)->toBe('create');
    expect($permissions->items()[3]->resource)->toBe('user');
    expect($permissions->items()[3]->action)->toBe('delete');
});

it('paginates permissions with 10 items per page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Permission::factory()->count(25)->create();

    $component = Livewire::test(Index::class);
    $permissions = $component->viewData('permissions');

    expect($permissions->perPage())->toBe(10);
    expect($permissions->items())->toHaveCount(10);
    expect($permissions->total())->toBe(25);
});

it('can delete permission when no roles are attached', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $permission = Permission::factory()->create();

    expect(Permission::count())->toBe(1);

    Livewire::test(Index::class)
        ->call('delete', $permission->id)
        ->assertHasNoErrors()
        ->assertDispatched('permission-deleted');

    expect(Permission::count())->toBe(0);
});

it('cannot delete permission when roles are attached', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $permission = Permission::factory()->create();
    $role = Role::factory()->create();
    
    // Attach permission to role
    $role->permissions()->attach($permission->id);

    expect(Permission::count())->toBe(1);
    expect($permission->roles()->count())->toBe(1);

    Livewire::test(Index::class)
        ->call('delete', $permission->id)
        ->assertDispatched('show-error-toast', [
            'message' => '역할에 할당된 권한은 삭제할 수 없습니다.'
        ]);

    // Permission should still exist
    expect(Permission::count())->toBe(1);
});

it('resets pagination after deleting permission', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create enough permissions to have multiple pages
    Permission::factory()->count(15)->create();
    $permission = Permission::factory()->create();

    $component = Livewire::test(Index::class);
    
    // Navigate to page 2
    $component->call('gotoPage', 2);
    
    // Verify we're on page 2 by checking pagination state
    $viewData = $component->viewData('permissions');
    expect($viewData->currentPage())->toBe(2);

    $component->call('delete', $permission->id)
        ->assertHasNoErrors();

    // Page should be reset to 1
    $viewData = $component->viewData('permissions');
    expect($viewData->currentPage())->toBe(1);
});

it('can trigger edit permission modal', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $permission = Permission::factory()->create();

    Livewire::test(Index::class)
        ->call('edit', $permission)
        ->assertDispatched('open-edit-permission', permissionId: $permission->id);
});

it('refreshes after permission is created', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Permission::factory()->count(15)->create();

    $component = Livewire::test(Index::class);
    
    // Navigate to page 2
    $component->call('gotoPage', 2);
    
    // Verify we're on page 2
    $viewData = $component->viewData('permissions');
    expect($viewData->currentPage())->toBe(2);

    // Simulate permission created event
    $component->dispatch('permission-created');

    // Page should be reset
    $viewData = $component->viewData('permissions');
    expect($viewData->currentPage())->toBe(1);
});

it('refreshes after permission is updated', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Permission::factory()->count(15)->create();

    $component = Livewire::test(Index::class);
    
    // Navigate to page 2
    $component->call('gotoPage', 2);
    
    // Verify we're on page 2
    $viewData = $component->viewData('permissions');
    expect($viewData->currentPage())->toBe(2);

    // Simulate permission updated event
    $component->dispatch('permission-updated');

    // Page should be reset
    $viewData = $component->viewData('permissions');
    expect($viewData->currentPage())->toBe(1);
});

it('throws exception when trying to delete non-existent permission', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(Index::class)
        ->call('delete', 99999)
        ->assertHasErrors();
})->throws('Illuminate\Database\Eloquent\ModelNotFoundException');