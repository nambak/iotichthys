<?php

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Permission>
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

        return [
            'name' => ucfirst($resource).' '.ucfirst($action),
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
            'name' => ucfirst($resource).' '.ucfirst($action),
            'resource' => $resource,
            'action' => $action,
        ]);
    }
}
