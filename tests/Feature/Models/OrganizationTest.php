<?php

use App\Models\Organization;

it('유효한 데이터로 조직을 생성 ', function () {
    $organization = Organization::factory()->create(
        ['name' => 'Test Organization']
    );

    expect($organization)
        ->toBeInstanceOf(Organization::class)
        ->name->toBe('Test Organization')
        ->slug->not->toBeEmpty();
});

it('slug는 사업자명으로 자동 생성', function () {
    $organization = Organization::factory()->create(
        ['name' => 'My Company Name']
    );

    expect($organization->slug)->toContain('my-company-name');
});
