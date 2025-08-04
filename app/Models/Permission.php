<?php

namespace App\Models;

use App\Events\PermissionCreating;
use App\Events\PermissionUpdating;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions')
            ->withTimestamps();
    }

    /**
     * Get all users who have this permission through their roles
     */
    public function users()
    {
        return User::whereHas('roles.permissions', function ($query) {
            $query->where('permissions.id', $this->id);
        });
    }
}
