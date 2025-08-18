<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    /**
     * 사용자가 속한 조직
     *
     * @return BelongsToMany
     */
    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'user_organizations')
            ->withPivot('is_owner')
            ->withTimestamps();
    }

    /**
     * 사용자가 속한 팀
     *
     * @return BelongsToMany
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'user_teams')
            ->withPivot('is_leader')
            ->withTimestamps();
    }

    /**
     * 사용자의 역할
     *
     * @return BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withPivot('roleable_id', 'roleable_type')
            ->withTimestamps();
    }

    /**
     * 특정 역할을 가지고 있는지 확인
     */
    public function hasRole(string|array $roles, ?string $scope = null, ?int $scopeId = null): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        $query = $this->roles()->whereIn('slug', $roles);

        if ($scope && $scopeId) {
            $query->wherePivot('roleable_type', $scope)
                ->wherePivot('roleable_id', $scopeId);
        } elseif (! $scope && ! $scopeId) {
            // 시스템 역할만 확인
            $query->where('is_system_role', true);
        }

        return $query->exists();
    }

    /**
     * 활성 사용자인지 확인
     */
    public function isActive(): bool
    {
        return ! $this->trashed();
    }

    /**
     * 탈퇴한 사용자인지 확인
     */
    public function isWithdrawn(): bool
    {
        return $this->trashed();
    }

    /**
     * 사용자 탈퇴 처리
     */
    public function withdraw(): bool
    {
        return $this->delete();
    }

    /**
     * 사용자 상태 표시용 텍스트
     */
    public function getStatusText(): string
    {
        return $this->trashed() ? '탈퇴' : '활성';
    }

    /**
     * 수정 가능한 사용자인지 확인
     */
    public function canBeEdited(): bool
    {
        return $this->isActive();
    }

    /**
     * 사용자에게 직접 할당된 권한
     *
     * @return BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user')
            ->withTimestamps();
    }

    /**
     * 사용자의 카테고리 접근 권한
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categoryAccessControls()
    {
        return $this->hasMany(CategoryAccessControl::class);
    }

    /**
     * 특정 권한을 가지고 있는지 확인
     */
    public function hasPermission(string|array $permissions): bool
    {
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        // 시스템 관리자는 모든 권한을 가짐
        if ($this->hasRole('system-admin')) {
            return true;
        }

        // 직접 할당된 권한 확인
        $hasDirectPermission = $this->permissions()
            ->whereIn('slug', $permissions)
            ->exists();

        if ($hasDirectPermission) {
            return true;
        }

        // 사용자의 역할을 통해 권한 확인
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permissions) {
                $query->whereIn('slug', $permissions);
            })
            ->exists();
    }

    /**
     * 특정 카테고리에 접근 권한이 있는지 확인
     */
    public function hasAccessToCategory(Category $category): bool
    {
        return $category->hasUserAccess($this);
    }

    /**
     * 사용자가 접근 가능한 모든 카테고리를 반환
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAccessibleCategories()
    {
        // 직접 권한이 있는 카테고리들
        $directAccessCategories = Category::whereHas('accessControls', function ($query) {
            $query->where('user_id', $this->id);
        })->get();

        $accessibleCategories = collect();

        foreach ($directAccessCategories as $category) {
            // 직접 권한이 있는 카테고리 추가
            $accessibleCategories->push($category);

            // 부모 카테고리들도 접근 가능
            $parent = $category->parent;
            while ($parent) {
                if (! $accessibleCategories->contains('id', $parent->id)) {
                    $accessibleCategories->push($parent);
                }
                $parent = $parent->parent;
            }
        }

        return $accessibleCategories->unique('id');
    }

    /**
     * 사용자가 직접 권한을 가진 카테고리들을 반환
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDirectAccessCategories()
    {
        return Category::whereHas('accessControls', function ($query) {
            $query->where('user_id', $this->id);
        })->get();
    }
}
