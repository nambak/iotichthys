<?php

use App\Livewire\Organization\CreateModal;
use App\Livewire\Organization\Index;
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
        $phoneNumber = '02' . fake()->numerify('########');
        $businessRegisterNumber = fake()->numerify('##########');

        Livewire::test(CreateModal::class)
            ->set('owner', $owner)
            ->set('address', $address)
            ->set('name', $companyName)
            ->set('phoneNumber', $phoneNumber)
            ->set('businessRegisterNumber', $businessRegisterNumber)
            ->call('save')
            ->assertSet('showCreateModal', false)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('organizations', [
            'owner'                    => $owner,
            'address'                  => $address,
            'name'                     => $companyName,
            'phone_number'             => $phoneNumber,
            'business_register_number' => $businessRegisterNumber,
        ]);
    });
});