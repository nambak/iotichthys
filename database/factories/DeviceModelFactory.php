<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeviceModel>
 */
class DeviceModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true).' 모델',
            'manufacturer' => $this->faker->randomElement(['삼성', 'LG', 'SKT', 'KT', 'Lotte', '현대', '기아', '네이버', '카카오', 'NCSOFT']),
            'description' => $this->faker->sentence(),
            'specifications' => [
                '온도 센서 ('.$this->faker->numberBetween(-20, 100).'°C)',
                '습도 센서 (0-100%)',
                'WiFi 연결 지원',
                $this->faker->randomElement(['Bluetooth 5.0', 'LoRa 통신', 'Zigbee 지원']),
            ],
        ];
    }
}
