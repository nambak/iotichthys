<?php

use App\Models\Category;
use App\Models\CategoryAccessControl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('카테고리 접근 권한 시스템', function () {
    test('직접 권한: 카테고리 C에 권한 부여 시 C에 접근 가능', function () {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => 'Category C']);

        // 권한 부여
        CategoryAccessControl::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        expect($user->hasAccessToCategory($category))->toBeTrue();
        expect($category->hasUserAccess($user))->toBeTrue();
        expect($category->hasDirectUserAccess($user))->toBeTrue();
    });

    test('부모 접근: 카테고리 C에 권한이 있으면 부모 A에도 접근 가능', function () {
        $user = User::factory()->create();

        // 계층 구조: A -> C
        $parentA = Category::factory()->create(['name' => 'Category A']);
        $childC = Category::factory()->create([
            'name' => 'Category C',
            'parent_id' => $parentA->id,
        ]);

        // 자식 카테고리 C에만 권한 부여
        CategoryAccessControl::create([
            'user_id' => $user->id,
            'category_id' => $childC->id,
        ]);

        // 자식 C는 직접 접근 가능
        expect($user->hasAccessToCategory($childC))->toBeTrue();
        expect($childC->hasDirectUserAccess($user))->toBeTrue();

        // 부모 A는 하위 권한으로 접근 가능
        expect($user->hasAccessToCategory($parentA))->toBeTrue();
        expect($parentA->hasUserAccess($user))->toBeTrue();
        expect($parentA->hasDirectUserAccess($user))->toBeFalse();
    });

    test('형제 차단: 카테고리 C에 권한이 있어도 형제 B에는 접근 불가', function () {
        $user = User::factory()->create();

        // 계층 구조: A -> B, A -> C
        $parentA = Category::factory()->create(['name' => 'Category A']);
        $siblingB = Category::factory()->create([
            'name' => 'Category B',
            'parent_id' => $parentA->id,
        ]);
        $siblingC = Category::factory()->create([
            'name' => 'Category C',
            'parent_id' => $parentA->id,
        ]);

        // 카테고리 C에만 권한 부여
        CategoryAccessControl::create([
            'user_id' => $user->id,
            'category_id' => $siblingC->id,
        ]);

        // C는 접근 가능
        expect($user->hasAccessToCategory($siblingC))->toBeTrue();

        // 부모 A도 접근 가능
        expect($user->hasAccessToCategory($parentA))->toBeTrue();

        // 형제 B는 접근 불가
        expect($user->hasAccessToCategory($siblingB))->toBeFalse();
        expect($siblingB->hasUserAccess($user))->toBeFalse();
    });

    test('복잡한 계층 구조에서의 권한 테스트', function () {
        $user = User::factory()->create();

        // 계층 구조: A -> B -> C, A -> D -> E
        $rootA = Category::factory()->create(['name' => 'Root A']);
        $childB = Category::factory()->create(['name' => 'Child B', 'parent_id' => $rootA->id]);
        $grandchildC = Category::factory()->create(['name' => 'Grandchild C', 'parent_id' => $childB->id]);
        $childD = Category::factory()->create(['name' => 'Child D', 'parent_id' => $rootA->id]);
        $grandchildE = Category::factory()->create(['name' => 'Grandchild E', 'parent_id' => $childD->id]);

        // Grandchild C에만 권한 부여
        CategoryAccessControl::create([
            'user_id' => $user->id,
            'category_id' => $grandchildC->id,
        ]);

        // C와 그 상위들은 접근 가능
        expect($user->hasAccessToCategory($grandchildC))->toBeTrue();
        expect($user->hasAccessToCategory($childB))->toBeTrue();
        expect($user->hasAccessToCategory($rootA))->toBeTrue();

        // D와 E는 접근 불가
        expect($user->hasAccessToCategory($childD))->toBeFalse();
        expect($user->hasAccessToCategory($grandchildE))->toBeFalse();
    });

    test('getAccessibleCategories 메서드가 올바른 카테고리들을 반환', function () {
        $user = User::factory()->create();

        // 계층 구조: A -> B -> C, A -> D
        $rootA = Category::factory()->create(['name' => 'Root A']);
        $childB = Category::factory()->create(['name' => 'Child B', 'parent_id' => $rootA->id]);
        $grandchildC = Category::factory()->create(['name' => 'Grandchild C', 'parent_id' => $childB->id]);
        $childD = Category::factory()->create(['name' => 'Child D', 'parent_id' => $rootA->id]);

        // C에 권한 부여
        CategoryAccessControl::create([
            'user_id' => $user->id,
            'category_id' => $grandchildC->id,
        ]);

        $accessibleCategories = $user->getAccessibleCategories();
        $accessibleIds = $accessibleCategories->pluck('id')->toArray();

        // A, B, C는 접근 가능해야 함
        expect($accessibleIds)->toContain($rootA->id);
        expect($accessibleIds)->toContain($childB->id);
        expect($accessibleIds)->toContain($grandchildC->id);

        // D는 접근 불가해야 함
        expect($accessibleIds)->not()->toContain($childD->id);
    });

    test('getDirectAccessCategories 메서드가 직접 권한이 있는 카테고리만 반환', function () {
        $user = User::factory()->create();

        $categoryA = Category::factory()->create(['name' => 'Category A']);
        $categoryB = Category::factory()->create(['name' => 'Category B']);

        // B에만 직접 권한 부여
        CategoryAccessControl::create([
            'user_id' => $user->id,
            'category_id' => $categoryB->id,
        ]);

        $directAccessCategories = $user->getDirectAccessCategories();

        expect($directAccessCategories)->toHaveCount(1);
        expect($directAccessCategories->first()->id)->toBe($categoryB->id);
    });

    test('getAuthorizedUsers 메서드가 해당 카테고리에 직접 권한이 있는 사용자들만 반환', function () {
        $user1 = User::factory()->create(['name' => 'User 1']);
        $user2 = User::factory()->create(['name' => 'User 2']);
        $user3 = User::factory()->create(['name' => 'User 3']);

        $category = Category::factory()->create(['name' => 'Test Category']);

        // user1과 user2에게 권한 부여
        CategoryAccessControl::create(['user_id' => $user1->id, 'category_id' => $category->id]);
        CategoryAccessControl::create(['user_id' => $user2->id, 'category_id' => $category->id]);

        $authorizedUsers = $category->getAuthorizedUsers();
        $authorizedUserIds = $authorizedUsers->pluck('id')->toArray();

        expect($authorizedUsers)->toHaveCount(2);
        expect($authorizedUserIds)->toContain($user1->id);
        expect($authorizedUserIds)->toContain($user2->id);
        expect($authorizedUserIds)->not()->toContain($user3->id);
    });

    test('권한이 없는 사용자는 어떤 카테고리에도 접근할 수 없음', function () {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => 'Test Category']);

        expect($user->hasAccessToCategory($category))->toBeFalse();
        expect($category->hasUserAccess($user))->toBeFalse();
        expect($user->getAccessibleCategories())->toHaveCount(0);
        expect($user->getDirectAccessCategories())->toHaveCount(0);
    });
});
