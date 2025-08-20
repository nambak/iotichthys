<?php

use App\Models\Category;
use Livewire\Livewire;

test('카테고리 생성 시 자동으로 slug가 생성됨', function () {
    Livewire::test('category.create-modal')
        ->set('name', 'Test Category')
        ->set('description', 'Test Description')
        ->call('save');

    $category = Category::where('name', 'Test Category')->first();

    expect($category)->not->toBeNull();
    expect($category->slug)->toBe('test-category');
    expect($category->description)->toBe('Test Description');
});

test('중복된 이름으로 카테고리 생성 시 고유한 slug가 생성됨', function () {
    // 첫 번째 카테고리 생성
    Category::create([
        'name' => 'Duplicate Category',
        'slug' => 'duplicate-category', // 직접 지정
    ]);

    // 두 번째 카테고리 생성 (같은 이름)
    Livewire::test('category.create-modal')
        ->set('name', 'Duplicate Category')
        ->call('save');

    $categories = Category::where('name', 'Duplicate Category')->get();

    expect($categories)->toHaveCount(2);
    expect($categories->pluck('slug')->toArray())->toContain('duplicate-category');
    expect($categories->pluck('slug')->unique())->toHaveCount(2); // 모든 slug가 고유함
});

test('하위 카테고리 생성 시 이벤트가 올바르게 발송됨', function () {
    $parent = Category::create([
        'name' => 'Parent Category',
        'slug' => 'parent-category',
    ]);

    Livewire::test('category.create-modal')
        ->set('name', 'Child Category')
        ->set('parent_id', $parent->id)
        ->call('save')
        ->assertDispatched('subcategory-created');

    $child = Category::where('name', 'Child Category')->first();
    expect($child)->not->toBeNull();
    expect($child->parent_id)->toBe($parent->id);
});

test('최상위 카테고리 생성 시 이벤트가 올바르게 발송됨', function () {
    Livewire::test('category.create-modal')
        ->set('name', 'Root Category')
        ->call('save')
        ->assertDispatched('category-created');

    $category = Category::where('name', 'Root Category')->first();
    expect($category)->not->toBeNull();
    expect($category->parent_id)->toBeNull();
});

test('유효성 검사가 정상 작동함', function () {
    Livewire::test('category.create-modal')
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name']);
});

test('하위 카테고리 모달 열기가 정상 작동함', function () {
    $parent = Category::create([
        'name' => 'Parent Category',
        'slug' => 'parent-category',
    ]);

    $component = Livewire::test('category.create-modal');

    $component->dispatch('open-create-subcategory', parentId: $parent->id);

    expect($component->get('parent_id'))->toBe($parent->id);
});
