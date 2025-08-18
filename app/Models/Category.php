<?php

namespace App\Models;

use App\Events\CategoryCreating;
use App\Events\CategoryUpdating;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    protected $dispatchesEvents = [
        'creating' => CategoryCreating::class,
        'updating' => CategoryUpdating::class,
    ];

    /**
     * 부모 카테고리와의 관계 (다대일)
     *
     * @return BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * 하위 카테고리와의 관계 (일대다)
     *
     * @return HasMany
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * 모든 하위 카테고리 (재귀적)
     *
     * @return HasMany
     */
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    /**
     * 카테고리 접근 권한과의 관계 (일대다)
     */
    public function accessControls(): HasMany
    {
        return $this->hasMany(CategoryAccessControl::class);
    }

    /**
     * 최상위 카테고리만 조회하는 스코프
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * 활성화된 카테고리만 조회하는 스코프
     *
     * @param  Builder  $query
     * @return Builder
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

    /**
     * 사용자가 이 카테고리에 직접 접근 권한이 있는지 확인
     */
    public function hasDirectUserAccess(User $user): bool
    {
        return $this->accessControls()->where('user_id', $user->id)->exists();
    }

    /**
     * 사용자가 이 카테고리에 접근 권한이 있는지 확인 (직접 권한 또는 하위 카테고리 권한을 통해)
     */
    public function hasUserAccess(User $user): bool
    {
        // 직접 권한 확인
        if ($this->hasDirectUserAccess($user)) {
            return true;
        }

        // 하위 카테고리에 권한이 있는지 재귀적으로 확인
        return $this->hasChildAccessForUser($user);
    }

    /**
     * 사용자가 하위 카테고리에 권한이 있는지 재귀적으로 확인
     */
    private function hasChildAccessForUser(User $user): bool
    {
        foreach ($this->children as $child) {
            if ($child->hasDirectUserAccess($user) || $child->hasChildAccessForUser($user)) {
                return true;
            }
        }

        // 하위 카테고리 및 모든 하위 카테고리에 권한이 있는지 확인 (N+1 문제 없이)
        $descendantIds = $this->getAllDescendantIds();
        if (empty($descendantIds)) {
            return false;
        }

        return $this->accessControls()
            ->where('user_id', $user->id)
            ->whereIn('category_id', $descendantIds)
            ->exists();
    }

    /**
     * 현재 카테고리의 모든 하위(자손) 카테고리 ID를 반환
     */
    public function getAllDescendantIds(): array
    {
        // 모든 카테고리를 한 번에 가져와서 메모리에서 트리 탐색
        $allCategories = self::all(['id', 'parent_id']);
        $descendantIds = [];
        $stack = [$this->id];
        $categoryMap = [];
        foreach ($allCategories as $cat) {
            $categoryMap[$cat->parent_id][] = $cat->id;
        }
        while ($stack) {
            $parentId = array_pop($stack);
            if (! empty($categoryMap[$parentId])) {
                foreach ($categoryMap[$parentId] as $childId) {
                    $descendantIds[] = $childId;
                    $stack[] = $childId;
                }
            }
        }

        return $descendantIds;
    }

    /**
     * 이 카테고리에 직접 접근 권한이 있는 사용자들을 반환
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAuthorizedUsers()
    {
        return User::whereHas('categoryAccessControls', function ($query) {
            $query->where('category_id', $this->id);
        })->get();
    }
}
