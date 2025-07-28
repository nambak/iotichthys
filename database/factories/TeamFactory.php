<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Team>
 */
class TeamFactory extends Factory
{
    /**
     * @var class-string<\App\Models\Team>
     */
    protected $model = Team::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(2, true);
        
        return [
            'organization_id' => Organization::factory(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->randomNumber(3),
            'description' => fake()->sentence(),
        ];
    }
}