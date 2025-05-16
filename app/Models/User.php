<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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
            'password'          => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn(string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'user_organizations')
            ->withPivot('is_owner')
            ->withTimestamps();
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'user_teams')
            ->withPivot('is_leader')
            ->withTimestamps();
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withPivot('roleable_id', 'roleable_type')
            ->withTimestamps();
    }

    /**
     * 특정 범위(조직/팀)에서 사용자가 권한을 가지는지 확인하는 메소드
     * $scope는 Organization 또는 Team 모델 인스턴스
     *
     * @param $permission
     * @param $scope
     * @return bool
     */
    public function hasPermission($permission, $scope = null)
    {
        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)->first();
            if (!$permission) return false;
        }

        // 사용자의 모든 역할 가져오기 (전역 역할 + 특정 범위 역할)
        $roles = $this->roles;

        if ($scope) {
            // 특정 범위에서의 역할만 필터링
            $roles = $roles->filter(function ($role) use ($scope) {
                return $role->pivot->roleable_id === $scope->id &&
                    $role->pivot->roleable_type === get_class($scope);
            });
        }

        // 역할들이 해당 권한을 가지는지 확인
        foreach ($roles as $role) {
            if ($role->permissions->contains('id', $permission->id)) {
                return true;
            }
        }

        return false;
    }
}
