<?php

use App\Models\Category;
use Livewire\Livewire;

test('displays category details correctly', function () {
    $category = Category::create([
        'name' => 'Electronics',
        'slug' => 'electronics',
        'description' => 'Electronic devices and gadgets',
    ]);

    Livewire::test('category.show', ['category' => $category])
        ->assertSee('Electronics')
        ->assertSee('Electronic devices and gadgets');
});

test('includes breadcrumb component', function () {
    $parent = Category::create([
        'name' => 'Electronics',
        'slug' => 'electronics',
    ]);

    $child = Category::create([
        'name' => 'Smartphones',
        'slug' => 'smartphones',
        'parent_id' => $parent->id,
    ]);

    $component = Livewire::test('category.show', ['category' => $child]);
    
    // Check that breadcrumb is rendered
    expect($component->html())
        ->toContain('카테고리')
        ->toContain('Electronics')
        ->toContain('Smartphones');
});

test('displays create subcategory button', function () {
    $category = Category::create([
        'name' => 'Electronics',
        'slug' => 'electronics',
    ]);

    Livewire::test('category.show', ['category' => $category])
        ->assertSee('하위 카테고리 추가');
});

test('can delete subcategory', function () {
    $parent = Category::create([
        'name' => 'Electronics',
        'slug' => 'electronics',
    ]);

    $child = Category::create([
        'name' => 'Smartphones',
        'slug' => 'smartphones',
        'parent_id' => $parent->id,
    ]);

    Livewire::test('category.subCategoryList', ['category' => $parent])
        ->call('deleteSubcategory', $child->id);

    $this->assertDatabaseMissing('categories', ['id' => $child->id]);
});

test('cannot delete subcategory with children', function () {
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

    Livewire::test('category.subCategoryList', ['category' => $grandparent])
        ->call('deleteSubcategory', $parent->id);

    // Parent should still exist because it has children
    $this->assertDatabaseHas('categories', ['id' => $parent->id]);
});

test('cannot delete subcategory that is not direct child', function () {
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

    // Try to delete grandchild from grandparent (should fail)
    Livewire::test('category.subCategoryList', ['category' => $grandparent])
        ->call('deleteSubcategory', $child->id);

    // Child should still exist
    $this->assertDatabaseHas('categories', ['id' => $child->id]);
});

test('dispatches events on subcategory actions', function () {
    $parent = Category::create([
        'name' => 'Electronics',
        'slug' => 'electronics',
    ]);

    $child = Category::create([
        'name' => 'Smartphones',
        'slug' => 'smartphones',
        'parent_id' => $parent->id,
    ]);

    Livewire::test('category.subCategoryList', ['category' => $parent])
        ->call('deleteSubcategory', $child->id)
        ->assertDispatched('subcategory-deleted');
});

test('dispatches create subcategory event', function () {
    $category = Category::create([
        'name' => 'Electronics',
        'slug' => 'electronics',
    ]);

    Livewire::test('category.subCategoryList', ['category' => $category])
        ->call('createSubcategory')
        ->assertDispatched('open-create-subcategory', parentId: $category->id);
});

test('dispatches edit subcategory event', function () {
    $parent = Category::create([
        'name' => 'Electronics',
        'slug' => 'electronics',
    ]);

    $child = Category::create([
        'name' => 'Smartphones',
        'slug' => 'smartphones',
        'parent_id' => $parent->id,
    ]);

    Livewire::test('category.show', ['category' => $parent])
        ->call('editSubcategory', $child)
        ->assertDispatched('open-edit-category', categoryId: $child->id);
});