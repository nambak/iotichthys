<?php

namespace App\Models;

use App\Events\TeamCreating;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'name',
        'slug',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot 메서드 - 모델 이벤트 등록
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        // Team 생성 이벤트
        static::creating(function ($team) {
            event(new TeamCreating($team));
        });
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_teams')
            ->withPivot('is_leader')
            ->withTimestamps();
    }

    public function roles()
    {
        return $this->morphMany(Role::class, 'scopeable');
    }
}
