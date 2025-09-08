<?php

use App\Livewire\Organization\AddUserModal;
use App\Livewire\Organization\Show;
use App\Livewire\Organization\UserList;
use App\Models\Organization;
use App\Models\User;
use Livewire\Livewire;

describe('조직 상세 페이지', function () {
    it('조직 정보를 올바르게 표시한다', function () {
        $user = User::factory()->create();
        $organization = Organization::factory()->create([
            'name' => '테스트 조직',
            'owner' => '홍길동',
            'business_register_number' => '1234567890',
            'phone_number' => '02-1234-5678',
            'address' => '서울시 강남구',
            'detail_address' => '테헤란로 123',
            'postcode' => '12345',
        ]);

        $this->actingAs($user);

        Livewire::test(Show::class, ['organization' => $organization])
            ->assertSee($organization->name)
            ->assertSee($organization->owner)
            ->assertSee($organization->business_register_number)
            ->assertSee($organization->phone_number)
            ->assertSee($organization->address)
            ->assertSee($organization->detail_address)
            ->assertSee($organization->postcode);
    });

    it('조직에 속한 사용자 목록을 표시한다', function () {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        // 조직에 사용자들 추가
        $member1 = User::factory()->create(['name' => '구성원1']);
        $member2 = User::factory()->create(['name' => '구성원2']);
        $owner = User::factory()->create(['name' => '조직소유자']);

        $organization->users()->attach($member1->id, ['is_owner' => false]);
        $organization->users()->attach($member2->id, ['is_owner' => false]);
        $organization->users()->attach($owner->id, ['is_owner' => true]);

        $this->actingAs($user);

        Livewire::test(Show::class, ['organization' => $organization])
            ->assertSee('구성원1')
            ->assertSee('구성원2')
            ->assertSee('조직소유자')
            ->assertSee('소유자')
            ->assertSee('구성원');
    });

    it('조직에 구성원이 없을 때 적절한 메시지를 표시한다', function () {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        $this->actingAs($user);

        Livewire::test(Show::class, ['organization' => $organization])
            ->assertSee('조직에 속한 구성원이 없습니다')
            ->assertSee('새로운 구성원을 추가해보세요.');
    });
});

describe('사용자 검색 기능', function () {
    it('유효한 이메일로 사용자를 검색할 수 있다', function () {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        User::factory()->create(['email' => 'test@example.com', 'name' => '테스트 사용자']);

        $this->actingAs($user);

        Livewire::test(AddUserModal::class, ['organization' => $organization])
            ->set('email', 'test@example.com')
            ->call('searchUser')
            ->assertHasNoErrors()
            ->assertSet('foundUser.email', 'test@example.com')
            ->assertSet('foundUser.name', '테스트 사용자');
    });

    it('존재하지 않는 이메일로 검색하면 오류 메시지가 표시된다', function () {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        $this->actingAs($user);

        Livewire::test(AddUserModal::class, ['organization' => $organization])
            ->set('email', 'nonexistent@example.com')
            ->call('searchUser')
            ->assertHasErrors(['email']);
    });

    it('잘못된 이메일 형식은 유효성 검사 오류가 발생한다', function () {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        $this->actingAs($user);

        Livewire::test(AddUserModal::class, ['organization' => $organization])
            ->set('email', 'invalid-email')
            ->call('searchUser')
            ->assertHasErrors(['email']);
    });

    it('빈 이메일로 검색하면 유효성 검사 오류가 발생한다', function () {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        $this->actingAs($user);

        Livewire::test(AddUserModal::class, ['organization' => $organization])
            ->set('email', '')
            ->call('searchUser')
            ->assertHasErrors(['email']);
    });

    it('이미 조직에 속한 사용자를 검색하면 오류 메시지가 표시된다', function () {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $existingMember = User::factory()->create(['email' => 'existing@example.com']);

        // 이미 조직에 속한 사용자로 설정
        $organization->users()->attach($existingMember->id, ['is_owner' => false]);

        $this->actingAs($user);

        Livewire::test(AddUserModal::class, ['organization' => $organization])
            ->set('email', 'existing@example.com')
            ->call('searchUser')
            ->assertHasErrors(['email']);
    });
});

describe('사용자 추가 기능', function () {
    it('검색된 사용자를 조직에 추가할 수 있다', function () {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $targetUser = User::factory()->create(['email' => 'test@example.com']);

        $this->actingAs($user);

        expect($organization->users()->count())->toBe(0);

        Livewire::test(AddUserModal::class, ['organization' => $organization])
            ->set('email', 'test@example.com')
            ->call('searchUser')
            ->call('addUserToOrganization')
            ->assertDispatched('user-added-to-organization');

        expect($organization->fresh()->users()->count())->toBe(1);
        expect($organization->users()->first()->id)->toBe($targetUser->id);
    });

    it('사용자를 찾지 않고 추가하려고 하면 오류 메시지가 표시된다', function () {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        $this->actingAs($user);

        Livewire::test(AddUserModal::class, ['organization' => $organization])
            ->call('addUserToOrganization')
            ->assertHasErrors(['email']);
    });

    it('사용자 추가 후 검색 상태가 초기화된다', function () {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $targetUser = User::factory()->create(['email' => 'test@example.com']);

        $this->actingAs($user);

        Livewire::test(AddUserModal::class, ['organization' => $organization])
            ->set('email', 'test@example.com')
            ->call('searchUser')
            ->assertSet('foundUser.id', $targetUser->id)
            ->call('addUserToOrganization')
            ->assertSet('email', '')
            ->assertSet('foundUser', null);
    });
});

describe('사용자 제거 기능', function () {
    it('일반 구성원을 조직에서 제거할 수 있다', function () {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $member = User::factory()->create();

        // 조직에 구성원 추가
        $organization->users()->attach($member->id, ['is_owner' => false]);

        $this->actingAs($user);

        expect($organization->users()->count())->toBe(1);

        Livewire::test(UserList::class, ['organization' => $organization])
            ->call('removeUserFromOrganization', $member->id)
            ->assertDispatched('user-removed-from-organization');

        expect($organization->fresh()->users()->count())->toBe(0);
    });

    it('조직 소유자는 제거할 수 없다', function () {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $owner = User::factory()->create();

        // 조직에 소유자 추가
        $organization->users()->attach($owner->id, ['is_owner' => true]);

        $this->actingAs($user);

        expect($organization->users()->count())->toBe(1);

        Livewire::test(UserList::class, ['organization' => $organization])
            ->call('removeUserFromOrganization', $owner->id)
            ->assertDispatched('show-error-toast');

        expect($organization->fresh()->users()->count())->toBe(1);
    });
});

describe('모달 관리', function () {
    it('검색 상태를 초기화할 수 있다', function () {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $targetUser = User::factory()->create(['email' => 'test@example.com']);

        $this->actingAs($user);

        Livewire::test(AddUserModal::class, ['organization' => $organization])
            ->set('email', 'test@example.com')
            ->call('searchUser')
            ->assertSet('foundUser.id', $targetUser->id)
            ->call('resetUserSearch')
            ->assertSet('userEmail', '')
            ->assertSet('foundUser', null);
    });
});
