<?php

use App\Models\Organization;
use App\Models\User;
use Laravel\Dusk\Browser;

test('팀 생성 모달이 열리고 닫힐 때 입력 필드가 초기화되어야 함', function () {
    $user = User::factory()->create();
    $organization = Organization::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/teams') // 팀 관리 페이지로 이동
            ->click('@create-team-button') // 모달 열기 버튼
            ->pause(1000)
            ->select('select[wire\\:model="organization_id"]', '1')
            ->type('input[wire\\:model="name"]', 'Test Team')
            ->type('input[wire\\:model="slug"]', 'test-team')
            ->type('textarea[wire\\:model="description"]', 'Test Description')
            ->pause(1000)
            // 모달 닫기
            ->click('ui-close[data-flux-modal-close] button[data-flux-button]')
            ->pause(1000)
            ->click('@create-team-button') // 모달 다시 열기
            ->pause(1000)
            // 필드들이 초기화되었는지 확인
            ->assertSelected('select[wire\\:model="organization_id"]', '')
            ->assertInputValue('input[wire\\:model="name"]', '')
            ->assertInputValue('input[wire\\:model="slug"]', '')
            ->assertInputValue('textarea[wire\\:model="description"]', '');
    });
});

test('팀명 입력 시 슬러그가 자동으로 생성되어야 함', function () {
    $user = User::factory()->create();
    $organization = Organization::factory()->create();

    $this->browse(function (Browser $browser) use ($user, $organization) {
        $browser->loginAs($user)
            ->visit('/teams')
            ->click('@create-team-button')
            ->pause(1000)
            ->select('select[wire\\:model="organization_id"]', $organization->id)
            ->type('input[wire\\:model="name"]', 'My Awesome Team')
            ->pause(2000) // 라이브와이어 업데이트 대기
            ->assertInputValue('input[wire\\:model="slug"]', 'my-awesome-team');
    });
});
