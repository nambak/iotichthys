<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Device>
 */
class DeviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' 장치',
            'device_id' => strtoupper($this->faker->unique()->lexify('DEVICE_???_???')),
            'device_model_id' => null, // 테스트에서 직접 설정
            'status' => $this->faker->randomElement(['active', 'inactive', 'maintenance', 'error']),
            'organization_id' => null, // 테스트에서 직접 설정
            'description' => $this->faker->sentence(),
            'location' => $this->faker->address(),
        ];
    }
}
