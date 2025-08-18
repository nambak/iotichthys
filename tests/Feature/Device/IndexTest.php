<?php

use App\Livewire\Device\Index;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\Organization;
use App\Models\User;
use Livewire\Livewire;

describe('장치 관리', function () {
    it('인증된 사용자는 장치 목록 페이지에 접근할 수 있다', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/devices')
            ->assertStatus(200)
            ->assertSeeLivewire(Index::class);
    });

    it('장치 목록이 정상적으로 표시된다', function () {
        $user = User::factory()->create();
        $deviceModel = DeviceModel::create([
            'name' => '테스트 모델',
            'description' => '테스트용 장치 모델'
        ]);
        
        $device = Device::create([
            'name' => '테스트 장치',
            'device_id' => 'TEST_DEVICE_001',
            'device_model_id' => $deviceModel->id,
            'status' => 'active'
        ]);

        $this->actingAs($user);

        Livewire::test(Index::class)
            ->assertSee('테스트 장치')
            ->assertSee('TEST_DEVICE_001')
            ->assertSee('테스트 모델')
            ->assertSee('활성');
    });

    it('장치 삭제가 정상적으로 작동한다', function () {
        $user = User::factory()->create();
        $deviceModel = DeviceModel::create([
            'name' => '테스트 모델',
            'description' => '테스트용 장치 모델'
        ]);
        
        $device = Device::create([
            'name' => '테스트 장치',
            'device_id' => 'TEST_DEVICE_001',
            'device_model_id' => $deviceModel->id,
            'status' => 'active'
        ]);

        $this->actingAs($user);

        expect(Device::count())->toBe(1);

        Livewire::test(Index::class)
            ->call('delete', $device->id)
            ->assertDispatched('device-deleted');

        expect(Device::count())->toBe(0);
    });

    it('장치 생성 버튼이 표시된다', function () {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(Index::class)
            ->assertSee('새 장치 추가');
    });

    it('빈 장치 목록일 때 적절한 메시지가 표시된다', function () {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(Index::class)
            ->assertSee('등록된 장치가 없습니다');
    });
});