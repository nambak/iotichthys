<?php

use App\Livewire\DeviceModel\Index;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\User;
use Livewire\Livewire;

describe('장치 모델 관리', function () {
    it('인증된 사용자는 장치 모델 목록 페이지에 접근할 수 있다', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/device-models')
            ->assertStatus(200)
            ->assertSeeLivewire(Index::class);
    });

    it('장치 모델 목록이 정상적으로 표시된다', function () {
        $user = User::factory()->create();
        $deviceModel = DeviceModel::create([
            'name' => '테스트 모델',
            'description' => '테스트용 장치 모델',
            'specifications' => ['온도 센서', 'WiFi 지원'],
        ]);

        $this->actingAs($user);

        Livewire::test(Index::class)
            ->assertSee('테스트 모델')
            ->assertSee('테스트용 장치 모델');
    });

    it('장치 모델 삭제가 정상적으로 작동한다', function () {
        $user = User::factory()->create();
        $deviceModel = DeviceModel::create([
            'name' => '삭제 테스트 모델',
            'description' => '삭제 테스트용 장치 모델',
        ]);

        $this->actingAs($user);

        expect(DeviceModel::count())->toBe(1);

        Livewire::test(Index::class)
            ->call('delete', $deviceModel->id)
            ->assertDispatched('device-model-deleted');

        expect(DeviceModel::count())->toBe(0);
    });

    it('연결된 장치가 있는 모델은 삭제할 수 없다', function () {
        $user = User::factory()->create();
        $deviceModel = DeviceModel::create([
            'name' => '사용중인 모델',
            'description' => '사용중인 장치 모델',
        ]);

        // 이 모델을 사용하는 장치 생성
        Device::create([
            'name' => '테스트 장치',
            'device_id' => 'TEST_DEVICE_001',
            'device_model_id' => $deviceModel->id,
            'status' => 'active',
        ]);

        $this->actingAs($user);

        expect(DeviceModel::count())->toBe(1);
        expect(Device::count())->toBe(1);

        Livewire::test(Index::class)
            ->call('delete', $deviceModel->id)
            ->assertDispatched('show-error-toast');

        // 모델은 삭제되지 않아야 함
        expect(DeviceModel::count())->toBe(1);
        expect(Device::count())->toBe(1);
    });

    it('장치 모델 생성 버튼이 표시된다', function () {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(Index::class)
            ->assertSee('새 모델 추가');
    });

    it('빈 장치 모델 목록일 때 적절한 메시지가 표시된다', function () {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(Index::class)
            ->assertSee('등록된 장치 모델이 없습니다');
    });
});
