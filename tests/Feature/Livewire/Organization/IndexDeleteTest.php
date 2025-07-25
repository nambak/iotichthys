<?php

use App\Livewire\Organization\Index;
use App\Models\Organization;
use App\Models\User;
use Livewire\Livewire;

it('can delete organization when no users are attached', function () {
    $user = User::factory()->create();
    $organization = Organization::factory()->create();

    $this->actingAs($user);

    expect(Organization::count())->toBe(1);

    Livewire::test(Index::class)
        ->call('delete', $organization->id)
        ->assertHasNoErrors();

    expect(Organization::count())->toBe(0);
});

it('cannot delete organization when users are attached', function () {
    $user = User::factory()->create();
    $organization = Organization::factory()->create();
    
    // Attach user to organization
    $organization->users()->attach($user->id);

    $this->actingAs($user);

    expect(Organization::count())->toBe(1);
    expect($organization->users()->count())->toBe(1);

    Livewire::test(Index::class)
        ->call('delete', $organization->id)
        ->assertDispatched('show-error-toast');

    // Organization should still exist
    expect(Organization::count())->toBe(1);
});

it('shows correct error message when organization has users', function () {
    $user = User::factory()->create();
    $organization = Organization::factory()->create();
    
    // Attach user to organization
    $organization->users()->attach($user->id);

    $this->actingAs($user);

    Livewire::test(Index::class)
        ->call('delete', $organization->id)
        ->assertDispatched('show-error-toast', [
            'message' => __('messages.organization_delete_has_users')
        ]);
});

it('shows success message when organization is deleted successfully', function () {
    $user = User::factory()->create();
    $organization = Organization::factory()->create();

    $this->actingAs($user);

    Livewire::test(Index::class)
        ->call('delete', $organization->id)
        ->assertDispatched('organization-deleted');
});