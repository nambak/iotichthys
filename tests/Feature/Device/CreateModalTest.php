<?php

use App\Livewire\Device\CreateModal;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\Organization;
use App\Models\User;
use Livewire\Livewire;

describe('장치 생성', function () {
    it('유효한 데이터로 장치를 생성할 수 있다', function () {
        $user = User::factory()->create();
        $deviceModel = DeviceModel::create([
            'name' => '테스트 모델',
            'description' => '테스트용 장치 모델'
        ]);
        $organization = Organization::create([
            'name' => '테스트 조직',
            'owner' => '테스트 사용자',
            'business_register_number' => '1234567890',
            'address' => '테스트 주소',
            'phone_number' => '02-1234-5678',
            'slug' => 'test-org'
        ]);

        $this->actingAs($user);

        Livewire::test(CreateModal::class)
            ->set('name', '테스트 장치')
            ->set('device_id', 'TEST_DEVICE_001')
            ->set('device_model_id', $deviceModel->id)
            ->set('status', 'active')
            ->set('organization_id', $organization->id)
            ->set('description', '테스트 설명')
            ->set('location', '테스트 위치')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('device-created');

        $this->assertDatabaseHas('devices', [
            'name' => '테스트 장치',
            'device_id' => 'TEST_DEVICE_001',
            'device_model_id' => $deviceModel->id,
            'status' => 'active',
            'organization_id' => $organization->id,
            'description' => '테스트 설명',
            'location' => '테스트 위치'
        ]);
    });

    it('필수 필드 검증이 동작한다', function () {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(CreateModal::class)
            ->set('name', '')
            ->set('device_id', '')
            ->set('device_model_id', '')
            ->call('save')
            ->assertHasErrors(['name', 'device_id', 'device_model_id']);
    });

    it('중복된 장치 ID는 허용되지 않는다', function () {
        $user = User::factory()->create();
        $deviceModel = DeviceModel::create([
            'name' => '테스트 모델',
            'description' => '테스트용 장치 모델'
        ]);

        // 기존 장치 생성
        Device::create([
            'name' => '기존 장치',
            'device_id' => 'DUPLICATE_ID',
            'device_model_id' => $deviceModel->id,
            'status' => 'active'
        ]);

        $this->actingAs($user);

        Livewire::test(CreateModal::class)
            ->set('name', '새 장치')
            ->set('device_id', 'DUPLICATE_ID')
            ->set('device_model_id', $deviceModel->id)
            ->call('save')
            ->assertHasErrors(['device_id']);
    });

    it('폼 리셋이 정상적으로 동작한다', function () {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Livewire::test(CreateModal::class)
            ->set('name', '테스트')
            ->set('device_id', 'TEST')
            ->call('resetForm');

        expect($component->get('name'))->toBe('');
        expect($component->get('device_id'))->toBe('');
        expect($component->get('status'))->toBe('active');
    });
});