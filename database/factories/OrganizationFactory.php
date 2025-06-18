<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * @var class-string<\App\Models\Organization>
     */
    protected $model = Organization::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'                     => fake()->company(),
            'owner'                    => fake()->name(),
            'business_register_number' => fake()->numerify('###########'),
            'address'                  => fake()->address(),
            'phone_number'             => fake()->phoneNumber(),
            'slug'                     => fake()->unique()->slug(),
        ];
    }
}
