<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug', 'description'];

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_organizations')
            ->withPivot('is_owner')
            ->withTimestamps();
    }

    public function roles()
    {
        return $this->morphMany(Role::class, 'scopeable');
    }
}
