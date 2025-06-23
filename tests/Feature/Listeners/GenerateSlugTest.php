<?php

use App\Events\OrganizationCreating;
use App\Listeners\GenerateSlug;
use App\Models\Organization;
use Psr\Log\LoggerInterface;

beforeEach(function () {
    $this->mockLogger = Mockery::mock(LoggerInterface::class);
    $this->mockLogger->shouldReceive('warning')->byDefault();

    $this->listener = new GenerateSlug($this->mockLogger);
});

afterEach(function () {
    Mockery::close();
});

it('slug가 비어있을 경우, 사업자명으로 slug 생성', function () {
    // Given
    $organization = new Organization(['name' => '유명 상사']);
    $event = new OrganizationCreating($organization);

    // When
    $this->listener->handle($event);

    // Then
    expect($organization->slug)->toBe('yumyeong-sangsa');
});

it('이미 slug가 존재할 경우 새로운 slug 생성', function () {
    // Given
    Organization::factory()->create([
        'name' => 'Test Organization',
        'slug' => 'test-organization'
    ]);

    $newOrganization = new Organization(['name' => 'Test Organization']);
    $event = new OrganizationCreating($newOrganization);

    // When
    $this->listener->handle($event);

    // Then
    expect($newOrganization->slug)
        ->not->toBe('test-organization')
        ->toContain('test-organization');
});
