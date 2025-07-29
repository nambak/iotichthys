<?php

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Permission>
 */
class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $resource = $this->faker->word();
        $action = $this->faker->word();
        $unique = $this->faker->unique()->randomNumber(5);
        
        return [
            'name' => ucfirst($resource) . ' ' . ucfirst($action),
            'slug' => $resource . '_' . $action . '_' . $unique,
            'resource' => $resource,
            'action' => $action,
            'description' => $this->faker->sentence(),
        ];
    }

    /**
     * Create a permission with specific resource and action.
     */
    public function withResourceAction(string $resource, string $action): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => ucfirst($resource) . ' ' . ucfirst($action),
            'slug' => $resource . '_' . $action,
            'resource' => $resource,
            'action' => $action,
        ]);
    }
}