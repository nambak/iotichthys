<?php

use App\Livewire\Organization\EditModal;
use App\Models\Organization;
use App\Models\User;
use Livewire\Livewire;

describe('organization/edit-modal 컴포넌트', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        
        $this->organization = Organization::factory()->create([
            'name' => '테스트 조직',
            'owner' => '홍길동',
            'address' => '서울시 강남구 테헤란로 123',
            'postcode' => '12345',
            'detail_address' => '상세주소',
            'phone_number' => '0212345678',
            'business_register_number' => '1234567890'
        ]);
    });

    it('컴포넌트가 정상적으로 렌더링 된다', function () {
        Livewire::test(EditModal::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.organization.edit-modal');
    });

    it('기존 조직 데이터를 정상적으로 로드한다', function () {
        Livewire::test(EditModal::class)
            ->call('openEdit', $this->organization->id)
            ->assertSet('name', '테스트 조직')
            ->assertSet('owner', '홍길동')
            ->assertSet('address', '서울시 강남구 테헤란로 123')
            ->assertSet('postcode', '12345')
            ->assertSet('detail_address', '상세주소')
            ->assertSet('phone_number', '0212345678')
            ->assertSet('business_register_number', '1234567890');
    });

    it('조직 정보를 성공적으로 수정한다', function () {
        $updateData = [
            'name' => '수정된 조직',
            'owner' => '김철수',
            'address' => '부산시 해운대구 센텀로 456',
            'postcode' => '54321',
            'detail_address' => '수정된 상세주소',
            'phone_number' => '0519876543',
            'business_register_number' => '0987654321'
        ];

        Livewire::test(EditModal::class)
            ->call('openEdit', $this->organization->id)
            ->set('name', $updateData['name'])
            ->set('owner', $updateData['owner'])
            ->set('address', $updateData['address'])
            ->set('postcode', $updateData['postcode'])
            ->set('detail_address', $updateData['detail_address'])
            ->set('phone_number', $updateData['phone_number'])
            ->set('business_register_number', $updateData['business_register_number'])
            ->call('update')
            ->assertHasNoErrors()
            ->assertDispatched('organization-updated');

        $this->assertDatabaseHas('organizations', array_merge(['id' => $this->organization->id], $updateData));
    });
});

describe('유효성 검증 테스트 - 수정', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        
        $this->organization = Organization::factory()->create();
    });

    describe('조직 이름 검증', function () {
        it('이름이 비어있으면 에러 발생', function () {
            Livewire::test(EditModal::class)
                ->call('openEdit', $this->organization->id)
                ->set('name', '')
                ->set('owner', fake()->name)
                ->set('business_register_number', fake()->unique()->numerify('##########'))
                ->set('address', fake()->address)
                ->set('postcode', fake()->postcode)
                ->set('detail_address', fake()->address)
                ->set('phone_number', '02' . fake()->numerify('########'))
                ->call('update')
                ->assertHasErrors(['name' => 'required'])
                ->assertSee('사업자명을 입력해 주세요');
        });

        it('이름이 2자 미만이면 에러 발생', function () {
            Livewire::test(EditModal::class)
                ->call('openEdit', $this->organization->id)
                ->set('name', '가')
                ->set('owner', fake()->name)
                ->set('business_register_number', fake()->unique()->numerify('##########'))
                ->set('address', fake()->address)
                ->set('postcode', fake()->postcode)
                ->set('detail_address', fake()->address)
                ->set('phone_number', '02' . fake()->numerify('########'))
                ->call('update')
                ->assertHasErrors(['name' => 'min'])
                ->assertSee('사업자명은 최소 2글자 최대 30글자 입니다');
        });

        it('이름이 30자 초과하면 에러 발생', function () {
            Livewire::test(EditModal::class)
                ->call('openEdit', $this->organization->id)
                ->set('name', '가나다라바마바사아자차카타파하가나다라바마바사아자차카타파하가')
                ->set('owner', fake()->name)
                ->set('business_register_number', fake()->unique()->numerify('##########'))
                ->set('address', fake()->address)
                ->set('postcode', fake()->postcode)
                ->set('detail_address', fake()->address)
                ->set('phone_number', '02' . fake()->numerify('########'))
                ->call('update')
                ->assertHasErrors(['name' => 'max'])
                ->assertSee('사업자명은 최소 2글자 최대 30글자 입니다');
        });
    });

    describe('사업자번호 검증', function () {
        it('이미 등록된 사업자번호로 수정하면 에러 발생', function () {
            // 다른 조직 생성
            $existingBusinessNumber = '9876543210';
            Organization::factory()->create([
                'business_register_number' => $existingBusinessNumber
            ]);

            Livewire::test(EditModal::class)
                ->call('openEdit', $this->organization->id)
                ->set('name', fake()->company)
                ->set('owner', fake()->name)
                ->set('business_register_number', $existingBusinessNumber)
                ->set('address', fake()->address)
                ->set('postcode', fake()->postcode)
                ->set('detail_address', fake()->address)
                ->set('phone_number', '02' . fake()->numerify('########'))
                ->call('update')
                ->assertHasErrors(['business_register_number' => 'unique'])
                ->assertSee('이미 등록된 사업자번호입니다');
        });

        it('본인의 사업자번호로는 수정 가능', function () {
            $originalBusinessNumber = $this->organization->business_register_number;

            Livewire::test(EditModal::class)
                ->call('openEdit', $this->organization->id)
                ->set('name', '수정된 조직명')
                ->set('owner', fake()->name)
                ->set('business_register_number', $originalBusinessNumber)
                ->set('address', fake()->address)
                ->set('postcode', fake()->postcode)
                ->set('detail_address', fake()->address)
                ->set('phone_number', '02' . fake()->numerify('########'))
                ->call('update')
                ->assertHasNoErrors();
        });
    });

    describe('우편번호 검증', function () {
        it('우편번호가 5자리가 아니면 에러 발생', function () {
            Livewire::test(EditModal::class)
                ->call('openEdit', $this->organization->id)
                ->set('name', fake()->company)
                ->set('owner', fake()->name)
                ->set('business_register_number', fake()->unique()->numerify('##########'))
                ->set('address', fake()->address)
                ->set('postcode', '1234')
                ->set('detail_address', fake()->address)
                ->set('phone_number', '02' . fake()->numerify('########'))
                ->call('update')
                ->assertHasErrors(['postcode' => 'digits'])
                ->assertSee('5자리 숫자로된 우편번호를 입력해 주세요');
        });
    });
});