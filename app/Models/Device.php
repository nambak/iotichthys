<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'device_id',
        'device_model_id',
        'status',
        'organization_id',
        'description',
        'location',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * 장치 모델
     *
     * @return BelongsTo
     */
    public function deviceModel(): BelongsTo
    {
        return $this->belongsTo(DeviceModel::class, 'device_model_id');
    }

    /**
     * 소속 조직
     *
     * @return BelongsTo
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * 장치 설정들
     *
     * @return HasMany
     */
    public function configs(): HasMany
    {
        return $this->hasMany(DeviceConfig::class);
    }

    /**
     * 특정 유형의 설정들
     *
     * @param string $type
     * @return HasMany
     */
    public function configsByType(string $type): HasMany
    {
        return $this->configs()->where('config_type', $type);
    }

    /**
     * 장치 상태 텍스트
     *
     * @return string
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'active' => '활성',
            'inactive' => '비활성',
            'maintenance' => '점검중',
            'error' => '오류',
            default => '알 수 없음',
        };
    }

    /**
     * 장치가 활성 상태인지 확인
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && !$this->trashed();
    }

    /**
     * 특정 설정값 가져오기
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getConfigValue(string $key, $default = null)
    {
        $config = $this->configs()->where('config_key', $key)->first();
        return $config ? $config->config_value : $default;
    }

    /**
     * 설정값 설정하기
     *
     * @param string $type
     * @param string $key
     * @param mixed $value
     * @param string|null $unit
     * @param string|null $description
     * @return DeviceConfig
     */
    public function setConfig(string $type, string $key, $value, ?string $unit = null, ?string $description = null): DeviceConfig
    {
        return $this->configs()->updateOrCreate(
            ['config_key' => $key],
            [
                'config_type' => $type,
                'config_value' => $value,
                'unit' => $unit,
                'description' => $description,
            ]
        );
    }
}
