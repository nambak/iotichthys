<?php

namespace Tests\Unit;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_category()
    {
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'A test category',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);
    }

    /** @test */
    public function it_can_have_parent_child_relationships()
    {
        $parent = Category::create([
            'name' => 'Parent Category',
            'slug' => 'parent-category',
        ]);

        $child = Category::create([
            'name' => 'Child Category',
            'slug' => 'child-category',
            'parent_id' => $parent->id,
        ]);

        $this->assertEquals($parent->id, $child->parent->id);
        $this->assertTrue($parent->children->contains($child));
    }

    /** @test */
    public function it_can_check_if_category_can_be_deleted()
    {
        $parent = Category::create([
            'name' => 'Parent Category',
            'slug' => 'parent-category',
        ]);

        // Category without children can be deleted
        $this->assertTrue($parent->canBeDeleted());

        // Create a child category
        Category::create([
            'name' => 'Child Category',
            'slug' => 'child-category',
            'parent_id' => $parent->id,
        ]);

        // Category with children cannot be deleted
        $this->assertFalse($parent->canBeDeleted());
    }

    /** @test */
    public function it_can_get_top_level_categories()
    {
        $topLevel = Category::create([
            'name' => 'Top Level',
            'slug' => 'top-level',
        ]);

        $child = Category::create([
            'name' => 'Child',
            'slug' => 'child',
            'parent_id' => $topLevel->id,
        ]);

        $topLevelCategories = Category::topLevel()->get();

        $this->assertTrue($topLevelCategories->contains($topLevel));
        $this->assertFalse($topLevelCategories->contains($child));
    }

    /** @test */
    public function it_can_get_full_path()
    {
        $parent = Category::create([
            'name' => 'Parent',
            'slug' => 'parent',
        ]);

        $child = Category::create([
            'name' => 'Child',
            'slug' => 'child',
            'parent_id' => $parent->id,
        ]);

        $grandchild = Category::create([
            'name' => 'Grandchild',
            'slug' => 'grandchild',
            'parent_id' => $child->id,
        ]);

        $this->assertEquals('Parent', $parent->full_path);
        $this->assertEquals('Parent > Child', $child->full_path);
        $this->assertEquals('Parent > Child > Grandchild', $grandchild->full_path);
    }
}
