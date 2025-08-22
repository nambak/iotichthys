<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeviceModel extends Model
{
    use HasFactory;

    protected $table = 'device_models';

    protected $fillable = [
        'name',
        'manufacturer',
        'specifications',
        'description',
    ];

    protected $casts = [
        'specifications' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 이 모델을 사용하는 장치들
     */
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class, 'device_model_id');
    }

    /**
     * 모델 삭제 가능 여부 확인
     */
    public function canBeDeleted(): bool
    {
        return $this->devices()->count() === 0;
    }

    /**
     * 장치 수 반환
     */
    public function getDevicesCountAttribute(): int
    {
        return $this->devices()->count();
    }
}
