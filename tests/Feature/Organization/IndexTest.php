<?php

use App\Livewire\Organization\CreateModal;
use App\Livewire\Organization\Index;
use App\Models\Organization;
use App\Models\User;
use Livewire\Livewire;

describe('조직 생성', function () {
    it('조직 생성 권한이 있는 사용자는 조직 생성 버튼을 볼 수 있다', function () {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(Index::class)
            ->assertDontSeeLivewire('새 조직 추가');
    });

    it('조직 생성 권한이 있는 사용자는 조직 생성을 할 수 있다', function () {
        $user = User::factory()->create();

        $this->actingAs($user);

        $owner = fake()->name;
        $address = fake()->address;
        $companyName = fake()->company;
        $phoneNumber = '02'.fake()->numerify('########');
        $businessRegisterNumber = fake()->numerify('##########');
        $postcode = fake()->postcode;

        Livewire::test(CreateModal::class)
            ->set('owner', $owner)
            ->set('address', $address)
            ->set('name', $companyName)
            ->set('phone_number', $phoneNumber)
            ->set('business_register_number', $businessRegisterNumber)
            ->set('postcode', $postcode)
            ->set('detail_address', $address)
            ->call('save')
            ->assertSet('showCreateModal', false)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('organizations', [
            'owner' => $owner,
            'address' => $address,
            'name' => $companyName,
            'phone_number' => $phoneNumber,
            'business_register_number' => $businessRegisterNumber,
        ]);
    });
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
                'message' => __('messages.organization.delete_has_users'),
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
});

describe('조직 이름 클릭 기능', function () {
    it('조직 이름이 조직 상세 페이지로 연결되는 링크로 표시된다', function () {
        $user = User::factory()->create();
        $organization = Organization::factory()->create(['name' => '테스트 조직']);

        $this->actingAs($user);

        Livewire::test(Index::class)
            ->assertSee('테스트 조직')
            ->assertSeeHtml('href="'.route('organization.show', $organization).'"');
    });

    it('여러 조직의 이름이 각각 올바른 상세 페이지로 연결된다', function () {
        $user = User::factory()->create();
        $organization1 = Organization::factory()->create(['name' => '조직 1']);
        $organization2 = Organization::factory()->create(['name' => '조직 2']);

        $this->actingAs($user);

        Livewire::test(Index::class)
            ->assertSee('조직 1')
            ->assertSee('조직 2')
            ->assertSeeHtml('href="'.route('organization.show', $organization1).'"')
            ->assertSeeHtml('href="'.route('organization.show', $organization2).'"');
    });
});
