<?php

use App\Livewire\Category\AddUserModal;
use App\Livewire\Category\Permissions;
use App\Models\Category;
use App\Models\CategoryAccessControl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

describe('카테고리 권한 관리 컴포넌트', function () {
    test('컴포넌트가 정상적으로 렌더링된다', function () {
        $category = Category::factory()->create();
        
        Livewire::test(Permissions::class, ['category' => $category])
            ->assertStatus(200);
    });

    test('권한이 있는 사용자와 없는 사용자가 올바르게 표시된다', function () {
        $category = Category::factory()->create();
        $userWithAccess = User::factory()->create(['name' => 'User With Access']);

        // 한 사용자에게 권한 부여
        CategoryAccessControl::create([
            'user_id' => $userWithAccess->id,
            'category_id' => $category->id,
        ]);

        Livewire::test(Permissions::class, ['category' => $category])
            ->assertSee($userWithAccess->name)
            ->assertSee('권한 취소');
    });

    test('사용자에게 권한을 부여할 수 있다', function () {
        $category = Category::factory()->create();
        $user = User::factory()->create(['name' => 'Test User']);

        Livewire::test(AddUserModal::class, ['category' => $category])
            ->set('foundUser', $user)
            ->call('grantAccess')
            ->assertDispatched('show-success-toast');

        // 데이터베이스에 권한이 저장되었는지 확인
        expect(CategoryAccessControl::where('user_id', $user->id)
            ->where('category_id', $category->id)
            ->exists()
        )->toBeTrue();
    });

    test('사용자의 권한을 취소할 수 있다', function () {
        $category = Category::factory()->create();
        $user = User::factory()->create(['name' => 'Test User']);
        
        // 권한 부여
        CategoryAccessControl::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        Livewire::test(Permissions::class, ['category' => $category])
            ->call('revokeAccess', $user->id)
            ->assertDispatched('show-success-toast');

        // 데이터베이스에서 권한이 삭제되었는지 확인
        expect(CategoryAccessControl::where('user_id', $user->id)
            ->where('category_id', $category->id)
            ->exists()
        )->toBeFalse();
    });

    test('검색 기능이 정상적으로 작동한다', function () {
        $category = Category::factory()->create();
        $userJohn = User::factory()->create(['name' => 'John Doe', 'email' => 'john@test.com']);
        $userJane = User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@test.com']);

        Livewire::test(AddUserModal::class, ['category' => $category])
            ->set('email', 'john@test.com')
            ->call('searchUser')
            ->assertSee($userJohn->name)
            ->assertDontSee($userJane->name)
            ->set('email', 'jane@test.com')
            ->call('searchUser')
            ->assertSee($userJane->name)
            ->assertDontSee($userJohn->name);
    });

    test('이미 권한이 있는 사용자에게 중복 권한 부여를 시도해도 에러가 발생하지 않는다', function () {
        $category = Category::factory()->create();
        $user = User::factory()->create(['name' => 'Test User']);
        
        // 권한 부여
        CategoryAccessControl::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        // 중복 권한 부여 시도
        Livewire::test(AddUserModal::class, ['category' => $category])
            ->call('grantAccess', $user->id);

        // 권한이 하나만 있는지 확인
        expect(CategoryAccessControl::where('user_id', $user->id)
            ->where('category_id', $category->id)
            ->count()
        )->toBe(1);
    });

    test('권한이 없는 사용자의 권한 취소를 시도해도 에러가 발생하지 않는다', function () {
        $category = Category::factory()->create();
        $user = User::factory()->create(['name' => 'Test User']);

        Livewire::test(Permissions::class, ['category' => $category])
            ->call('revokeAccess', $user->id)
            ->assertDispatched('show-success-toast');
    });

    test('권한 없는 메시지가 올바르게 표시된다', function () {
        $category = Category::factory()->create();

        Livewire::test(Permissions::class, ['category' => $category])
            ->assertSee('권한을 가진 사용자가 없습니다');
    });
});
