<?php

use App\Livewire\DeviceModel\CreateModal;
use App\Models\DeviceModel;
use App\Models\User;
use Livewire\Livewire;

describe('장치 모델 생성', function () {
    it('장치 모델을 성공적으로 생성할 수 있다', function () {
        $user = User::factory()->create();

        $this->actingAs($user);

        expect(DeviceModel::count())->toBe(0);

        Livewire::test(CreateModal::class)
            ->set('name', '새 테스트 모델')
            ->set('description', '새로운 테스트용 모델입니다')
            ->set('specifications', "온도 센서 (0~100°C)\n습도 센서 (0~100%)\nWiFi 연결 지원")
            ->call('save')
            ->assertDispatched('device-model-created');

        expect(DeviceModel::count())->toBe(1);

        $deviceModel = DeviceModel::first();
        expect($deviceModel->name)->toBe('새 테스트 모델');
        expect($deviceModel->description)->toBe('새로운 테스트용 모델입니다');
        expect($deviceModel->specifications)->toBeArray();
        expect($deviceModel->specifications)->toHaveCount(3);
    });

    it('필수 필드 검증이 작동한다', function () {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(CreateModal::class)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name']);
    });

    it('모델명 최대 길이 검증이 작동한다', function () {
        $user = User::factory()->create();

        $this->actingAs($user);

        $longName = str_repeat('a', 256); // 255자 초과

        Livewire::test(CreateModal::class)
            ->set('name', $longName)
            ->call('save')
            ->assertHasErrors(['name']);
    });

    it('사양이 비어있을 때도 모델을 생성할 수 있다', function () {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(CreateModal::class)
            ->set('name', '간단한 모델')
            ->set('specifications', '')
            ->call('save')
            ->assertDispatched('device-model-created');

        $deviceModel = DeviceModel::first();
        expect($deviceModel->specifications)->toBeNull();
    });

    it('모델 생성 후 모달이 닫힌다', function () {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(CreateModal::class)
            ->set('name', '모달 테스트 모델')
            ->set('description', '모달 닫힘 테스트')
            ->call('save')
            ->assertDispatched('modal-close')
            ->assertDispatched('device-model-created');
    });

    it('제조사 필드가 포함된 장치 모델을 생성할 수 있다', function () {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(CreateModal::class)
            ->set('name', '삼성 IoT 센서')
            ->set('manufacturer', '삼성전자')
            ->set('description', '삼성에서 제조한 IoT 센서')
            ->call('save')
            ->assertDispatched('device-model-created');

        $deviceModel = DeviceModel::first();
        expect($deviceModel->name)->toBe('삼성 IoT 센서');
        expect($deviceModel->manufacturer)->toBe('삼성전자');
        expect($deviceModel->description)->toBe('삼성에서 제조한 IoT 센서');
    });

    it('제조사 필드를 비워두고도 모델을 생성할 수 있다', function () {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(CreateModal::class)
            ->set('name', '제조사 미상 센서')
            ->set('manufacturer', '')
            ->call('save')
            ->assertDispatched('device-model-created');

        $deviceModel = DeviceModel::first();
        expect($deviceModel->manufacturer)->toBe('');
    });

    it('제조사명 최대 길이 검증이 작동한다', function () {
        $user = User::factory()->create();

        $this->actingAs($user);

        $longManufacturer = str_repeat('a', 256); // 255자 초과

        Livewire::test(CreateModal::class)
            ->set('name', '테스트 모델')
            ->set('manufacturer', $longManufacturer)
            ->call('save')
            ->assertHasErrors(['manufacturer']);
    });
});
