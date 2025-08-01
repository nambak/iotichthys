<?php

use App\Models\User;

test('새 사용자는 기본적으로 활성 상태이다', function () {
    $user = User::factory()->create();
    
    expect($user->isActive())->toBeTrue();
    expect($user->isWithdrawn())->toBeFalse();
    expect($user->status)->toBe(User::STATUS_ACTIVE);
    expect($user->withdrawn_at)->toBeNull();
});

test('사용자 탈퇴가 정상적으로 동작한다', function () {
    $user = User::factory()->create();
    
    $result = $user->withdraw();
    
    expect($result)->toBeTrue();
    expect($user->isWithdrawn())->toBeTrue();
    expect($user->isActive())->toBeFalse();
    expect($user->status)->toBe(User::STATUS_WITHDRAWN);
    expect($user->withdrawn_at)->not->toBeNull();
});

test('사용자 상태 텍스트가 올바르게 반환된다', function () {
    $activeUser = User::factory()->create();
    $withdrawnUser = User::factory()->create();
    $withdrawnUser->withdraw();
    
    expect($activeUser->getStatusText())->toBe('활성');
    expect($withdrawnUser->getStatusText())->toBe('탈퇴');
});

test('활성 사용자만 편집할 수 있다', function () {
    $activeUser = User::factory()->create();
    $withdrawnUser = User::factory()->create();
    $withdrawnUser->withdraw();
    
    expect($activeUser->canBeEdited())->toBeTrue();
    expect($withdrawnUser->canBeEdited())->toBeFalse();
});

test('사용자 상태 상수가 올바르게 정의되어 있다', function () {
    expect(User::STATUS_ACTIVE)->toBe('active');
    expect(User::STATUS_WITHDRAWN)->toBe('withdrawn');
});

test('탈퇴 시간이 현재 시간으로 설정된다', function () {
    $user = User::factory()->create();
    $beforeWithdraw = now();
    
    $user->withdraw();
    
    $afterWithdraw = now();
    
    expect($user->withdrawn_at)
        ->toBeGreaterThanOrEqual($beforeWithdraw)
        ->toBeLessThanOrEqual($afterWithdraw);
});

test('status 필드가 mass assignable에 포함되어 있다', function () {
    $fillable = (new User())->getFillable();
    
    expect($fillable)->toContain('status');
});

test('withdrawn_at 필드가 datetime으로 캐스팅된다', function () {
    $user = User::factory()->create();
    $user->withdraw();
    
    expect($user->withdrawn_at)->toBeInstanceOf(\Carbon\Carbon::class);
});
