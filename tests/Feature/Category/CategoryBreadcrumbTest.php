<?php

use App\Models\Category;
use Livewire\Livewire;

test('displays single level breadcrumb correctly', function () {
    $category = Category::create([
        'name' => 'Electronics',
        'slug' => 'electronics',
    ]);

    Livewire::test('category.category-breadcrumb', ['category' => $category])
        ->assertSee('카테고리')
        ->assertSee('Electronics');
});

test('displays two level breadcrumb correctly', function () {
    $parent = Category::create([
        'name' => 'Electronics',
        'slug' => 'electronics',
    ]);

    $child = Category::create([
        'name' => 'Smartphones',
        'slug' => 'smartphones',
        'parent_id' => $parent->id,
    ]);

    Livewire::test('category.category-breadcrumb', ['category' => $child])
        ->assertSee('카테고리')
        ->assertSee('Electronics')
        ->assertSee('Smartphones');
});

test('displays three level breadcrumb correctly', function () {
    $grandparent = Category::create([
        'name' => 'Electronics',
        'slug' => 'electronics',
    ]);

    $parent = Category::create([
        'name' => 'Smartphones',
        'slug' => 'smartphones',
        'parent_id' => $grandparent->id,
    ]);

    $child = Category::create([
        'name' => 'iPhone',
        'slug' => 'iphone',
        'parent_id' => $parent->id,
    ]);

    Livewire::test('category.category-breadcrumb', ['category' => $child])
        ->assertSee('카테고리')
        ->assertSee('Electronics')
        ->assertSee('Smartphones') 
        ->assertSee('iPhone');
});

test('generates correct breadcrumb structure', function () {
    $parent = Category::create([
        'name' => 'Electronics',
        'slug' => 'electronics',
    ]);

    $child = Category::create([
        'name' => 'Smartphones',
        'slug' => 'smartphones',
        'parent_id' => $parent->id,
    ]);

    $component = Livewire::test('category.category-breadcrumb', ['category' => $child]);
    
    // Check that breadcrumb includes category index link
    expect($component->html())->toContain(route('category.index'));
    
    // Check that parent category has a clickable link
    expect($component->html())->toContain(route('category.show', $parent));
});

test('handles deep nesting correctly', function () {
    // Create a 5-level deep category structure
    $level1 = Category::create(['name' => 'Level 1', 'slug' => 'level-1']);
    $level2 = Category::create(['name' => 'Level 2', 'slug' => 'level-2', 'parent_id' => $level1->id]);
    $level3 = Category::create(['name' => 'Level 3', 'slug' => 'level-3', 'parent_id' => $level2->id]);
    $level4 = Category::create(['name' => 'Level 4', 'slug' => 'level-4', 'parent_id' => $level3->id]);
    $level5 = Category::create(['name' => 'Level 5', 'slug' => 'level-5', 'parent_id' => $level4->id]);

    Livewire::test('category.category-breadcrumb', ['category' => $level5])
        ->assertSee('카테고리')
        ->assertSee('Level 1')
        ->assertSee('Level 2')
        ->assertSee('Level 3')
        ->assertSee('Level 4')
        ->assertSee('Level 5');
});