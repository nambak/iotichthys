<?php

namespace App\Models;

use App\Events\OrganizationCreating;
use App\Events\OrganizationUpdating;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'owner',
        'address',
        'postcode',
        'detail_address',
        'phone_number',
        'business_register_number',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot 메서드 - 모델 이벤트 등록
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($organization) {
            event(new OrganizationCreating($organization));
        });

        static::updating(function ($organization) {
            event(new OrganizationUpdating($organization));
        });
    }

    /**
     * 조직->팀(1:N) 관계 정의
     *
     * @return HasMany
     */
    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    /**
     * 사용자->조직(N:M) 관계 정의
     *
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_organizations')
            ->withPivot('is_owner')
            ->withTimestamps();
    }

    /**
     * 조직->역활(1:N, 다형성) 관계 정의
     *
     * @return MorphMany
     */
    public function roles()
    {
        return $this->morphMany(Role::class, 'scopeable');
    }
}
