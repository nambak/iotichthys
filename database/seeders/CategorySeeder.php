<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 기술 카테고리와 하위 카테고리들
        $tech = Category::create([
            'name' => '기술',
            'slug' => 'technology',
            'description' => '기술 관련 내용을 다루는 카테고리입니다.',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $programming = Category::create([
            'name' => '프로그래밍',
            'slug' => 'programming',
            'description' => '프로그래밍 관련 내용',
            'parent_id' => $tech->id,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'PHP',
            'slug' => 'php',
            'description' => 'PHP 프로그래밍 언어',
            'parent_id' => $programming->id,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Laravel',
            'slug' => 'laravel',
            'description' => 'Laravel 프레임워크',
            'parent_id' => $programming->id,
            'sort_order' => 2,
            'is_active' => true,
        ]);

        Category::create([
            'name' => '하드웨어',
            'slug' => 'hardware',
            'description' => '하드웨어 관련 내용',
            'parent_id' => $tech->id,
            'sort_order' => 2,
            'is_active' => true,
        ]);

        Category::create([
            'name' => '보안',
            'slug' => 'security',
            'description' => '보안 관련 내용',
            'parent_id' => $tech->id,
            'sort_order' => 3,
            'is_active' => true,
        ]);

        // 뉴스 카테고리와 하위 카테고리들
        $news = Category::create([
            'name' => '뉴스',
            'slug' => 'news',
            'description' => '뉴스 관련 카테고리',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        Category::create([
            'name' => '국내뉴스',
            'slug' => 'domestic-news',
            'description' => '국내 뉴스',
            'parent_id' => $news->id,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Category::create([
            'name' => '해외뉴스',
            'slug' => 'international-news',
            'description' => '해외 뉴스',
            'parent_id' => $news->id,
            'sort_order' => 2,
            'is_active' => true,
        ]);

        // 공지사항 카테고리
        Category::create([
            'name' => '공지사항',
            'slug' => 'announcements',
            'description' => '공지사항 카테고리',
            'sort_order' => 3,
            'is_active' => true,
        ]);
    }
}