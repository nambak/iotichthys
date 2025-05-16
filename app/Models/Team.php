<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['organization_id', 'name', 'slug', 'description'];

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
