<?php

use App\Models\User;
use Carbon\Carbon;

test('새 사용자는 기본적으로 활성 상태이다', function () {
    $user = User::factory()->create();
    
    expect($user->isActive())->toBeTrue();
    expect($user->isWithdrawn())->toBeFalse();
    expect($user->deleted_at)->toBeNull();
});

test('사용자 탈퇴가 정상적으로 동작한다', function () {
    $user = User::factory()->create();
    
    $result = $user->withdraw();
    
    expect($result)->toBeTrue();
    expect($user->isWithdrawn())->toBeTrue();
    expect($user->isActive())->toBeFalse();
    expect($user->deleted_at)->not->toBeNull();
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

test('탈퇴 시간이 현재 시간으로 설정된다', function () {
    $user = User::factory()->create();
    
    $user->withdraw();
    
    expect($user->deleted_at)->not->toBeNull();
    expect($user->deleted_at)->toBeInstanceOf(Carbon::class);
    // 방금 전에 설정되었으므로 현재 시간과 거의 같아야 함
    expect($user->deleted_at->diffInSeconds(now()))->toBeLessThan(5);
});

test('deleted_at 필드가 datetime으로 캐스팅된다', function () {
    $user = User::factory()->create();
    $user->withdraw();
    
    expect($user->deleted_at)->toBeInstanceOf(Carbon::class);
});
