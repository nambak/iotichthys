<?php

use App\Models\User;
use Laravel\Dusk\Browser;

test('모달을 닫을 때 입력 필드가 초기화되어야 함', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/organizations') // 조직 관리 페이지로 이동
            ->click('@create-organization-button') // 모달 열기 버튼
            ->waitFor('dialog[data-modal="create-organization"]', 10) // 모달 로딩 대기
            ->type('input[wire\\:model="name"]', 'Test Company')
            ->type('input[wire\\:model="owner"]', 'Test Owner')
            ->type('input[wire\\:model="businessRegisterNumber"]', '1234567890')
            ->type('input[wire\\:model="address"]', 'Test Address')
            ->type('input[wire\\:model="phoneNumber"]', '01012345678')
            // 입력 값 확인
            ->click('ui-close[data-flux-modal-close]') // 닫기 버튼
            ->waitUntilMissing('dialog[data-modal="create-organization"]', 10) // 모달 닫힘 대기
            ->click('@create-organization-button') // 모달 다시 열기
            ->waitFor('dialog[data-modal="create-organization"]', 10) // 모달 로딩 대기
            // 필드들이 초기화되었는지 확인
            ->assertInputValue('input[wire\\:model="name"]', '')
            ->assertInputValue('input[wire\\:model="owner"]', '')
            ->assertInputValue('input[wire\\:model="businessRegisterNumber"]', '')
            ->assertInputValue('input[wire\\:model="address"]', '')
            ->assertInputValue('input[wire\\:model="phoneNumber"]', '');
    });
});