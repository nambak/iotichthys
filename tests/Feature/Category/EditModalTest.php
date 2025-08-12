<?php

use App\Models\Category;
use Livewire\Livewire;

test('카테고리 수정 시 정보가 올바르게 업데이트됨', function () {
    $category = Category::create([
        'name' => 'Original Category',
        'slug' => 'original-category',
        'description' => 'Original description',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    Livewire::test('category.edit-modal')
        ->dispatch('open-edit-category', categoryId: $category->id)
        ->set('name', 'Updated Category')
        ->set('description', 'Updated description')
        ->set('sort_order', 2)
        ->set('is_active', false)
        ->call('update');

    $category->refresh();
    
    expect($category->name)->toBe('Updated Category');
    expect($category->description)->toBe('Updated description');
    expect($category->sort_order)->toBe(2);
    expect($category->is_active)->toBeFalse();
});

test('카테고리 이름 변경 시 slug가 자동으로 재생성됨', function () {
    $category = Category::create([
        'name' => 'Original Category',
        'slug' => 'original-category',
        'description' => 'Original description',
    ]);

    Livewire::test('category.edit-modal')
        ->dispatch('open-edit-category', categoryId: $category->id)
        ->set('name', 'Updated Category Name')
        ->call('update');

    $category->refresh();
    
    expect($category->name)->toBe('Updated Category Name');
    expect($category->slug)->toBe('updated-category-name');
});

test('카테고리 설명만 변경 시 slug는 변경되지 않음', function () {
    $category = Category::create([
        'name' => 'Test Category',
        'slug' => 'test-category',
        'description' => 'Original description',
    ]);

    $originalSlug = $category->slug;

    Livewire::test('category.edit-modal')
        ->dispatch('open-edit-category', categoryId: $category->id)
        ->set('description', 'Updated description only')
        ->call('update');

    $category->refresh();
    
    expect($category->description)->toBe('Updated description only');
    expect($category->slug)->toBe($originalSlug); // slug는 변경되지 않아야 함
});

test('카테고리 수정 시 이벤트가 올바르게 발송됨', function () {
    $category = Category::create([
        'name' => 'Test Category',
        'slug' => 'test-category',
    ]);

    Livewire::test('category.edit-modal')
        ->dispatch('open-edit-category', categoryId: $category->id)
        ->set('name', 'Updated Category')
        ->call('update')
        ->assertDispatched('category-updated');
});

test('카테고리 수정 시 모달이 닫힘', function () {
    $category = Category::create([
        'name' => 'Test Category',
        'slug' => 'test-category',
    ]);

    Livewire::test('category.edit-modal')
        ->dispatch('open-edit-category', categoryId: $category->id)
        ->set('name', 'Updated Category')
        ->call('update')
        ->assertDispatched('modal-close', modal: 'edit-category');
});

test('카테고리 수정 모달 열기가 정상 작동함', function () {
    $category = Category::create([
        'name' => 'Test Category',
        'slug' => 'test-category',
        'description' => 'Test description',
        'sort_order' => 5,
        'is_active' => false,
    ]);

    $component = Livewire::test('category.edit-modal');
    
    $component->dispatch('open-edit-category', categoryId: $category->id);
    
    expect($component->get('category')->id)->toBe($category->id);
    expect($component->get('name'))->toBe('Test Category');
    expect($component->get('description'))->toBe('Test description');
    expect($component->get('sort_order'))->toBe(5);
    expect($component->get('is_active'))->toBeFalse();
});

test('카테고리 수정 시 유효성 검사가 정상 작동함', function () {
    $category = Category::create([
        'name' => 'Test Category',
        'slug' => 'test-category',
    ]);

    Livewire::test('category.edit-modal')
        ->dispatch('open-edit-category', categoryId: $category->id)
        ->set('name', '') // 빈 이름으로 설정
        ->call('update')
        ->assertHasErrors(['name']);
});

test('존재하지 않는 카테고리로 수정 모달 열기 시도 시 오류 발생', function () {
    $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
    
    Livewire::test('category.edit-modal')
        ->dispatch('open-edit-category', categoryId: 99999);
});

test('중복된 이름으로 카테고리 수정 시 고유한 slug가 생성됨', function () {
    // 첫 번째 카테고리 생성
    $existingCategory = Category::create([
        'name' => 'Existing Category',
        'slug' => 'existing-category',
    ]);

    // 두 번째 카테고리 생성
    $categoryToUpdate = Category::create([
        'name' => 'Category To Update',
        'slug' => 'category-to-update',
    ]);

    // 두 번째 카테고리의 이름을 첫 번째와 같게 수정
    Livewire::test('category.edit-modal')
        ->dispatch('open-edit-category', categoryId: $categoryToUpdate->id)
        ->set('name', 'Existing Category')
        ->call('update');

    $categoryToUpdate->refresh();
    
    expect($categoryToUpdate->name)->toBe('Existing Category');
    expect($categoryToUpdate->slug)->not->toBe('existing-category'); // 다른 slug가 생성되어야 함
    expect($categoryToUpdate->slug)->toStartWith('existing-category-'); // 고유한 suffix가 추가되어야 함
});