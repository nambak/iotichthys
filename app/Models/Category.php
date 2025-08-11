<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 부모 카테고리와의 관계 (다대일)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * 하위 카테고리와의 관계 (일대다)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * 모든 하위 카테고리 (재귀적)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    /**
     * 최상위 카테고리만 조회하는 스코프
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * 활성화된 카테고리만 조회하는 스코프
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * 카테고리 삭제 가능 여부 확인
     *
     * @return bool
     */
    public function canBeDeleted()
    {
        // 하위 카테고리가 있는지 확인
        if ($this->children()->count() > 0) {
            return false;
        }

        // TODO: 컨텐츠가 있는지 확인 (컨텐츠 모델이 생성된 후 구현)
        // if ($this->contents()->count() > 0) {
        //     return false;
        // }

        return true;
    }

    /**
     * 카테고리의 전체 경로를 반환
     *
     * @return string
     */
    public function getFullPathAttribute()
    {
        $path = collect();
        $category = $this;

        while ($category) {
            $path->prepend($category->name);
            $category = $category->parent;
        }

        return $path->implode(' > ');
    }

    /**
     * 카테고리의 깊이 레벨을 반환
     *
     * @return int
     */
    public function getDepthLevelAttribute()
    {
        $level = 0;
        $category = $this->parent;

        while ($category) {
            $level++;
            $category = $category->parent;
        }

        return $level;
    }
}