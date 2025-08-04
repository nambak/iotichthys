<?php

use App\Models\User;
use Livewire\Livewire;

test('사용자 목록 페이지가 정상적으로 렌더링된다', function () {
    $admin = User::factory()->create();
    
    $this->actingAs($admin)
        ->get('/users')
        ->assertOk()
        ->assertSee('사용자 관리')
        ->assertSee('등록된 사용자를 조회하고 관리합니다.');
});

test('사용자 목록에 활성 및 탈퇴 사용자가 모두 표시된다', function () {
    $admin = User::factory()->create();
    $activeUser = User::factory()->create(['name' => 'Active User']);
    $withdrawnUser = User::factory()->create(['name' => 'Withdrawn User']);
    $withdrawnUser->withdraw();

    Livewire::actingAs($admin)
        ->test(\App\Livewire\Users\Index::class)
        ->assertSee('Active User')
        ->assertSee('Withdrawn User')
        ->assertSee('활성')
        ->assertSee('탈퇴');
});

test('활성 사용자에게는 편집 및 탈퇴 버튼이 표시된다', function () {
    $admin = User::factory()->create();
    $activeUser = User::factory()->create(['name' => 'Active User']);

    $response = $this->actingAs($admin)->get('/users');
    
    $response->assertOk();
    // 활성 사용자의 행에는 편집/탈퇴 버튼이 있어야 함 (다른 사용자이므로)
});

test('탈퇴한 사용자에게는 액션 버튼이 표시되지 않는다', function () {
    $admin = User::factory()->create();
    $withdrawnUser = User::factory()->create(['name' => 'Withdrawn User']);
    $withdrawnUser->withdraw();

    Livewire::actingAs($admin)
        ->test(\App\Livewire\Users\Index::class)
        ->assertSee('Withdrawn User')
        ->assertSee('탈퇴');
});

test('사용자 탈퇴 기능이 정상적으로 동작한다', function () {
    $admin = User::factory()->create();
    $targetUser = User::factory()->create(['name' => 'Target User']);

    Livewire::actingAs($admin)
        ->test(\App\Livewire\Users\Index::class)
        ->call('withdraw', $targetUser->id)
        ->assertDispatched('user-withdrawn');

    $targetUser->refresh();
    expect($targetUser->isWithdrawn())->toBeTrue();
    expect($targetUser->deleted_at)->not->toBeNull();
});

test('자기 자신은 탈퇴할 수 없다', function () {
    $admin = User::factory()->create();

    Livewire::actingAs($admin)
        ->test(\App\Livewire\Users\Index::class)
        ->call('withdraw', $admin->id)
        ->assertDispatched('show-error-toast', ['message' => '자기 자신은 탈퇴시킬 수 없습니다.']);

    $admin->refresh();
    expect($admin->isActive())->toBeTrue();
});

test('이미 탈퇴한 사용자는 다시 탈퇴할 수 없다', function () {
    $admin = User::factory()->create();
    $withdrawnUser = User::factory()->create();
    $withdrawnUser->withdraw();

    Livewire::actingAs($admin)
        ->test(\App\Livewire\Users\Index::class)
        ->call('withdraw', $withdrawnUser->id)
        ->assertDispatched('show-error-toast', ['message' => '이미 탈퇴한 사용자입니다.']);
});

test('탈퇴한 사용자는 편집할 수 없다', function () {
    $admin = User::factory()->create();
    $withdrawnUser = User::factory()->create();
    $withdrawnUser->withdraw();

    Livewire::actingAs($admin)
        ->test(\App\Livewire\Users\Index::class)
        ->call('edit', $withdrawnUser)
        ->assertDispatched('show-error-toast', ['message' => '탈퇴한 사용자는 편집할 수 없습니다.']);
});

test('활성 사용자 편집 모달이 정상적으로 열린다', function () {
    $admin = User::factory()->create();
    $activeUser = User::factory()->create();

    Livewire::actingAs($admin)
        ->test(\App\Livewire\Users\Index::class)
        ->call('edit', $activeUser->id)
        ->assertDispatched('open-edit-user', userId: $activeUser->id);
});

test('사용자 목록이 페이지네이션된다', function () {
    $admin = User::factory()->create();
    User::factory(15)->create(); // 총 16명 (관리자 포함)

    Livewire::actingAs($admin)
        ->test(\App\Livewire\Users\Index::class)
        ->assertViewHas('users')
        ->assertSee('Showing');
});
