<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'config_type',
        'config_key',
        'config_value',
        'unit',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 소속 장치
     *
     * @return BelongsTo
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * 설정 유형 텍스트
     *
     * @return string
     */
    public function getConfigTypeTextAttribute(): string
    {
        return match($this->config_type) {
            'threshold' => '임계값',
            'sampling_period' => '샘플링 주기',
            'setting' => '일반 설정',
            'alert' => '알림 설정',
            default => '알 수 없음',
        };
    }

    /**
     * 설정값이 숫자인지 확인
     *
     * @return bool
     */
    public function isNumericValue(): bool
    {
        return is_numeric($this->config_value);
    }

    /**
     * 설정값을 숫자로 반환 (가능한 경우)
     *
     * @return float|null
     */
    public function getNumericValue(): ?float
    {
        return $this->isNumericValue() ? (float) $this->config_value : null;
    }

    /**
     * 단위와 함께 설정값 반환
     *
     * @return string
     */
    public function getValueWithUnitAttribute(): string
    {
        $value = $this->config_value;
        if ($this->unit) {
            return $value . ' ' . $this->unit;
        }
        return $value;
    }
}
