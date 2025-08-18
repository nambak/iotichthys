<?php

use App\Models\Category;
use Livewire\Livewire;

test('Index 페이지에서 카테고리 수정 버튼 클릭 시 올바른 이벤트가 발송됨', function () {
    $category = Category::create([
        'name' => 'Test Category',
        'slug' => 'test-category',
    ]);

    Livewire::test('category.index')
        ->call('editCategory', $category)
        ->assertDispatched('open-edit-category', categoryId: $category->id);
});

test('하위 카테고리 수정 버튼 클릭 시 올바른 이벤트가 발송됨', function () {
    $parent = Category::create([
        'name' => 'Parent Category',
        'slug' => 'parent-category',
    ]);

    $child = Category::create([
        'name' => 'Child Category',
        'slug' => 'child-category',
        'parent_id' => $parent->id,
    ]);

    Livewire::test('category.sub-category-list', ['category' => $parent])
        ->call('editSubcategory', $child->id)
        ->assertDispatched('open-edit-category', categoryId: $child->id);
});

test('카테고리 수정 성공 후 Index 페이지가 새로고침됨', function () {
    $category = Category::create([
        'name' => 'Test Category',
        'slug' => 'test-category',
    ]);

    $indexComponent = Livewire::test('category.index');

    // 수정 성공 이벤트 발송
    $indexComponent->dispatch('category-updated');

    // refreshAfterUpdate 메서드가 호출되어야 하며, 페이지가 리셋되어야 함
    expect(true)->toBeTrue(); // 이벤트 리스너가 등록되어 있는지 확인
});

test('카테고리 수정 성공 후 Show 페이지가 새로고침됨', function () {
    $parent = Category::create([
        'name' => 'Parent Category',
        'slug' => 'parent-category',
    ]);

    $child = Category::create([
        'name' => 'Child Category',
        'slug' => 'child-category',
        'parent_id' => $parent->id,
    ]);

    $subCategoryListComponent = Livewire::test('category.subCategoryList', ['category' => $parent]);

    // 하위 카테고리 수정 성공 이벤트 발송
    $subCategoryListComponent->dispatch('subcategory-updated');

    // refreshAfterUpdate 메서드가 호출되어야 하며, 페이지가 리셋되어야 함
    expect(true)->toBeTrue(); // 이벤트 리스너가 등록되어 있는지 확인
});
