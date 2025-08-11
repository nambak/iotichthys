<?php

namespace App\Models;

use App\Events\PermissionCreating;
use App\Events\PermissionUpdating;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'resource', 'action', 'description'];

    /**
     * Boot 메서드 - 모델 이벤트 등록
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($permission) {
            event(new PermissionCreating($permission));
        });

        static::updating(function ($permission) {
            event(new PermissionUpdating($permission));
        });
    }

    /**
     * roles - permission 관계 (N:M)
     *
     * @return BelongsToMany
     */
    public function roles(): belongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions')
            ->withTimestamps();
    }

    /**
     * roles - users 관계 (N:M)
     *
     * @return BelongsToMany
     */
    public function users(): belongsToMany
    {
        return $this->belongsToMany(User::class, 'permission_user')
            ->withTimestamps();
    }

    /**
     * permission을 가진 user와 role을 조회
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function usersWithRoles(): \Illuminate\Database\Eloquent\Builder
    {
        return User::whereHas('roles.permissions', function ($query) {
            $query->where('permissions.id', $this->id);
        });
    }
}
