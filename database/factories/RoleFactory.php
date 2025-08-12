<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['admin', 'manager', 'user', 'viewer', 'editor']),
            'slug' => fn (array $attributes) => strtolower(str_replace(' ', '_', $attributes['name'])),
            'description' => $this->faker->sentence(),
        ];
    }
}