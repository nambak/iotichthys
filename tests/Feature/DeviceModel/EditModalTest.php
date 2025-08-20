<?php

use App\Livewire\DeviceModel\EditModal;
use App\Models\DeviceModel;
use App\Models\User;
use Livewire\Livewire;

describe('장치 모델 수정', function () {
    it('장치 모델을 성공적으로 수정할 수 있다', function () {
        $user = User::factory()->create();
        $deviceModel = DeviceModel::factory()->create([
            'name' => '기존 모델',
            'description' => '기존 설명',
            'specifications' => ['온도 센서', 'WiFi 지원']
        ]);

        $this->actingAs($user);

        Livewire::test(EditModal::class)
            ->call('openModal', $deviceModel->id)
            ->assertSet('deviceModel.id', $deviceModel->id)
            ->assertSet('name', '기존 모델')
            ->assertSet('description', '기존 설명')
            ->assertSet('specifications', "온도 센서\nWiFi 지원")
            ->set('name', '수정된 모델')
            ->set('description', '수정된 설명')
            ->set('specifications', "온도 센서 (개선)\n습도 센서\nWiFi 지원")
            ->call('save')
            ->assertDispatched('modal-close')
            ->assertDispatched('device-model-updated');

        $deviceModel->refresh();
        expect($deviceModel->name)->toBe('수정된 모델');
        expect($deviceModel->description)->toBe('수정된 설명');
        expect($deviceModel->specifications)->toHaveCount(3);
        expect($deviceModel->specifications[0])->toBe('온도 센서 (개선)');
        expect($deviceModel->specifications[1])->toBe('습도 센서');
        expect($deviceModel->specifications[2])->toBe('WiFi 지원');
    });

    it('필수 필드 검증이 작동한다', function () {
        $user = User::factory()->create();
        $deviceModel = DeviceModel::factory()->create();

        $this->actingAs($user);

        Livewire::test(EditModal::class)
            ->call('openModal', $deviceModel->id)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name']);
    });

    it('모델명 최대 길이 검증이 작동한다', function () {
        $user = User::factory()->create();
        $deviceModel = DeviceModel::factory()->create();

        $this->actingAs($user);

        $longName = str_repeat('a', 256); // 255자 초과

        Livewire::test(EditModal::class)
            ->call('openModal', $deviceModel->id)
            ->set('name', $longName)
            ->call('save')
            ->assertHasErrors(['name']);
    });

    it('사양을 비워서 수정할 수 있다', function () {
        $user = User::factory()->create();
        $deviceModel = DeviceModel::factory()->create([
            'specifications' => ['기존 사양1', '기존 사양2']
        ]);

        $this->actingAs($user);

        Livewire::test(EditModal::class)
            ->call('openModal', $deviceModel->id)
            ->set('specifications', '')
            ->call('save')
            ->assertDispatched('device-model-updated');

        $deviceModel->refresh();
        expect($deviceModel->specifications)->toBeNull();
    });

    it('존재하지 않는 모델 ID로 모달을 열 때 404 에러가 발생한다', function () {
        $user = User::factory()->create();

        $this->actingAs($user);

        expect(function () {
            Livewire::test(EditModal::class)
                ->call('openModal', 99999);
        })->toThrow(Illuminate\Database\Eloquent\ModelNotFoundException::class);
    });

    it('빈 specifications 배열을 문자열로 올바르게 변환한다', function () {
        $user = User::factory()->create();
        $deviceModel = DeviceModel::factory()->create([
            'specifications' => null
        ]);

        $this->actingAs($user);

        Livewire::test(EditModal::class)
            ->call('openModal', $deviceModel->id)
            ->assertSet('specifications', '');
    });
});