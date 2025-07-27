<?php

namespace Tests\Feature\Organization;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_organization_can_be_updated()
    {
        // Create a user and organization
        $user = User::factory()->create();
        $organization = Organization::factory()->create([
            'name' => 'Original Name',
            'owner' => 'Original Owner',
            'business_register_number' => '1234567890',
            'address' => 'Original Address',
            'phone_number' => '01012345678',
        ]);

        $updatedData = [
            'name' => 'Updated Name',
            'owner' => 'Updated Owner',
            'business_register_number' => '0987654321',
            'address' => 'Updated Address',
            'phone_number' => '01087654321',
            'postcode' => '12345',
            'detail_address' => 'Updated Detail Address',
        ];

        // Update the organization
        $organization->update($updatedData);

        // Assertions
        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'name' => 'Updated Name',
            'owner' => 'Updated Owner',
            'business_register_number' => '0987654321',
            'address' => 'Updated Address',
            'phone_number' => '01087654321',
            'postcode' => '12345',
            'detail_address' => 'Updated Detail Address',
        ]);
    }

    public function test_organization_can_be_soft_deleted()
    {
        $organization = Organization::factory()->create();

        $organization->delete();

        $this->assertSoftDeleted('organizations', [
            'id' => $organization->id,
        ]);
    }

    public function test_update_organization_request_validation()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create([
            'business_register_number' => '1234567890',
        ]);

        $this->actingAs($user);

        // Test that duplicate business register number is not allowed for different organization
        $anotherOrganization = Organization::factory()->create([
            'business_register_number' => '0987654321',
        ]);

        $response = $this->patch("/organizations/{$organization->id}", [
            'name' => 'Test Organization',
            'owner' => 'Test Owner',
            'business_register_number' => '0987654321', // This should fail
            'address' => 'Test Address with more than ten characters',
            'phone_number' => '01012345678',
        ]);

        // The request should fail validation
        $response->assertSessionHasErrors('business_register_number');
    }
}