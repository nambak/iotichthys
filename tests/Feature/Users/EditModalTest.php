<?php

use App\Livewire\Users\EditModal;
use App\Models\User;
use Livewire\Livewire;

test('사용자 편집 모달이 정상적으로 렌더링된다', function () {
    $component = Livewire::test(EditModal::class);

    $component->assertStatus(200);
});

test('편집 모달이 사용자 정보로 정상적으로 초기화된다', function () {
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    Livewire::test(EditModal::class)
        ->call('openEditModal', $user->id)
        ->assertSet('name', 'Test User')
        ->assertSet('email', 'test@example.com')
        ->assertSet('user.id', $user->id);
});

test('탈퇴한 사용자는 편집 모달을 열 수 없다', function () {
    $withdrawnUser = User::factory()->create();
    $withdrawnUser->withdraw();

    Livewire::test(EditModal::class)
        ->call('openEditModal', $withdrawnUser->id)
        ->assertDispatched('show-error-toast', ['message' => '탈퇴한 사용자는 편집할 수 없습니다.']);
});

test('유효한 데이터로 사용자 정보를 수정할 수 있다', function () {
    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
    ]);

    Livewire::test(EditModal::class)
        ->call('openEditModal', $user->id)
        ->set('name', 'Updated Name')
        ->set('email', 'updated@example.com')
        ->call('update')
        ->assertDispatched('user-updated');

    $user->refresh();
    expect($user->name)->toBe('Updated Name');
    expect($user->email)->toBe('updated@example.com');
});

test('이름 필드 유효성 검증이 동작한다', function () {
    $user = User::factory()->create();

    Livewire::test(EditModal::class)
        ->call('openEditModal', $user->id)
        ->set('name', '')
        ->call('update')
        ->assertHasErrors(['name' => 'required']);
});

test('이메일 필드 유효성 검증이 동작한다', function () {
    $user = User::factory()->create();

    Livewire::test(EditModal::class)
        ->call('openEditModal', $user->id)
        ->set('email', '')
        ->call('update')
        ->assertHasErrors(['email' => 'required']);

    Livewire::test(EditModal::class)
        ->call('openEditModal', $user->id)
        ->set('email', 'invalid-email')
        ->call('update')
        ->assertHasErrors(['email' => 'email']);
});

test('중복된 이메일로는 수정할 수 없다', function () {
    $existingUser = User::factory()->create(['email' => 'existing@example.com']);
    $targetUser = User::factory()->create(['email' => 'target@example.com']);

    Livewire::test(EditModal::class)
        ->call('openEditModal', $targetUser->id)
        ->set('email', 'existing@example.com')
        ->call('update')
        ->assertHasErrors(['email' => 'unique']);
});

test('같은 이메일로 수정하는 것은 허용된다', function () {
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    Livewire::test(EditModal::class)
        ->call('openEditModal', $user->id)
        ->set('name', 'Updated Name')
        ->set('email', 'test@example.com') // 같은 이메일
        ->call('update')
        ->assertHasNoErrors()
        ->assertDispatched('user-updated');

    $user->refresh();
    expect($user->name)->toBe('Updated Name');
});

test('이름 길이 검증이 동작한다', function () {
    $user = User::factory()->create();

    Livewire::test(EditModal::class)
        ->call('openEditModal', $user->id)
        ->set('name', str_repeat('a', 256)) // 255자 초과
        ->call('update')
        ->assertHasErrors(['name' => 'max']);
});

test('이메일 길이 검증이 동작한다', function () {
    $user = User::factory()->create();

    Livewire::test(EditModal::class)
        ->call('openEditModal', $user->id)
        ->set('email', str_repeat('a', 250).'@example.com') // 255자 초과
        ->call('update')
        ->assertHasErrors(['email' => 'max']);
});

test('폼 리셋이 정상적으로 동작한다', function () {
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $component = Livewire::test(EditModal::class)
        ->call('openEditModal', $user->id)
        ->set('name', 'Changed Name')
        ->set('email', 'changed@example.com');

    $component->call('resetForm')
        ->assertSet('name', '')
        ->assertSet('email', '')
        ->assertSet('user', null);
});
