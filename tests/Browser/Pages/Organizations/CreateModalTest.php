<?php

use App\Models\User;
use Laravel\Dusk\Browser;

test('모달을 닫을 때 입력 필드가 초기화되어야 함', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/organizations') // 조직 관리 페이지로 이동
            ->click('@create-organization-button') // 모달 열기 버튼
            ->pause(1000)
            ->type('input[wire\\:model="name"]', 'Test Company')
            ->type('input[wire\\:model="owner"]', 'Test Owner')
            ->type('input[wire\\:model="business_register_number"]', '1234567890')
            ->type('input[wire\\:model="address"]', 'Test Address')
            ->type('input[wire\\:model="phone_number"]', '01012345678')
            ->pause(1000)
            // 입력 값 확인
            ->click('ui-close[data-flux-modal-close] button[data-flux-button') // 닫기 버튼
            ->pause(1000)
            ->click('@create-organization-button') // 모달 다시 열기
            ->pause(1000)
            // 필드들이 초기화되었는지 확인
            ->assertInputValue('input[wire\\:model="name"]', '')
            ->assertInputValue('input[wire\\:model="owner"]', '')
            ->assertInputValue('input[wire\\:model="business_register_number"]', '')
            ->assertInputValue('input[wire\\:model="address"]', '')
            ->assertInputValue('input[wire\\:model="phone_number"]', '');
    });
});

test('주소 검색 버튼을 클릭하면 다음 카카오 검색 창이 떠야함', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        // 원래 창의 핸들 저장
        $originalWindow = $browser->driver->getWindowHandle();

        $browser->loginAs($user)
            ->visit('/organizations')
            ->click('@create-organization-button')
            ->waitFor('dialog[data-modal="create-organization"]', 10)
            ->click('@search-address-button')
            ->pause(2000);

        // 새 창이 열렸는지 확인
        $allWindows = $browser->driver->getWindowHandles();
        expect(count($allWindows))->toBe(2);
    });
});
