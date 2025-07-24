<?php

use App\Livewire\Organization\CreateModal;
use App\Models\Organization;
use Livewire\Livewire;

describe('organization/create-modal 컴포넌트', function () {
    it('컴포넌트가 정상적으로 렌더링 된다', function () {
        Livewire::test(CreateModal::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.organization.create-modal');
    });
});

describe('유효성 검증 테스트', function () {
    describe('조직 이름 검증', function () {
        it('이름이 비어있으면 에러 발생', function () {
            Livewire::test(CreateModal::class)
                ->set('name', '')
                ->set('owner', fake()->name)
                ->set('business_register_number', fake()->unique()->numerify('##########'))
                ->set('address', fake()->address)
                ->set('phone_number', '02' . fake()->numerify('########'))
                ->call('save')
                ->assertHasErrors(['name' => 'required'])
                ->assertSee('사업자명을 입력해 주세요');
        });

        it('이름이 2자 미만이면 에러 발생', function () {
            Livewire::test(CreateModal::class)
                ->set('name', '가')
                ->set('owner', fake()->name)
                ->set('business_register_number', fake()->unique()->numerify('##########'))
                ->set('address', fake()->address)
                ->set('phone_number', '02' . fake()->numerify('########'))
                ->call('save')
                ->assertHasErrors(['name' => 'min'])
                ->assertSee('사업자명은 최소 2글자 최대 30글자 입니다');
        });

        it('이름이 30자 초과하면 에러 발생', function () {
            Livewire::test(CreateModal::class)
                ->set('name', '가나다라바마바사아자차카타파하가나다라바마바사아자차카타파하가')
                ->set('owner', fake()->name)
                ->set('business_register_number', fake()->unique()->numerify('##########'))
                ->set('address', fake()->address)
                ->set('phone_number', '02' . fake()->numerify('########'))
                ->call('save')
                ->assertHasErrors(['name' => 'max'])
                ->assertSee('사업자명은 최소 2글자 최대 30글자 입니다');
        });

        it('이름이 30자면 검증을 통과', function () {
            Livewire::test(CreateModal::class)
                ->set('name', '가나다라바마바사아자차카타파하가나다라바마바사아자차카타파하')
                ->set('owner', fake()->name)
                ->set('business_register_number', fake()->unique()->numerify('##########'))
                ->set('address', fake()->address)
                ->set('phone_number', '02' . fake()->numerify('########'))
                ->call('save')
                ->assertHasNoErrors();
        });

        it('이름을 입력한 경우 검증을 통과', function () {
            Livewire::test(CreateModal::class)
                ->set('name', fake()->company)
                ->set('owner', fake()->name)
                ->set('business_register_number', fake()->unique()->numerify('##########'))
                ->set('address', fake()->address)
                ->set('phone_number', '02' . fake()->numerify('########'))
                ->call('save')
                ->assertHasNoErrors();
        });
    });

    describe('대표명 검증', function () {
        it('대표자명이 비어있으면 에러 발생', function () {
            Livewire::test(CreateModal::class)
                ->set('name', fake()->company)
                ->set('owner', '')
                ->set('business_register_number', fake()->unique()->numerify('##########'))
                ->set('address', fake()->address)
                ->set('phone_number', '02' . fake()->numerify('########'))
                ->call('save')
                ->assertHasErrors(['owner' => 'required'])
                ->assertSee('대표자명을 입력해 주세요');
        });

        it('대표자명이 2글자 미만이면 에러 발생', function () {
            Livewire::test(CreateModal::class)
                ->set('name', fake()->company)
                ->set('owner', '홍')
                ->set('business_register_number', fake()->unique()->numerify('##########'))
                ->set('address', fake()->address)
                ->set('phone_number', '02' . fake()->numerify('########'))
                ->call('save')
                ->assertHasErrors(['owner' => 'min'])
                ->assertSee('최소 2글자 이상 입력해 주세요');
        });

        it('대표자명을 입력한 경우 검증을 통과', function () {
            Livewire::test(CreateModal::class)
                ->set('name', fake()->company)
                ->set('owner', fake()->name)
                ->set('business_register_number', fake()->unique()->numerify('##########'))
                ->set('address', fake()->address)
                ->set('phone_number', '02' . fake()->numerify('########'))
                ->call('save')
                ->assertHasNoErrors();
        });
    });

    describe('사업자번호 검증', function () {
        it('사업자번호가 비어있으면 에러 발생', function () {
            Livewire::test(CreateModal::class)
                ->set('name', fake()->company)
                ->set('owner', fake()->name)
                ->set('business_register_number', '')
                ->set('address', fake()->address)
                ->set('phone_number', '02' . fake()->numerify('########'))
                ->call('save')
                ->assertHasErrors(['business_register_number' => 'required'])
                ->assertSee('사업자번호를 입력해 주세요');
        });

        it('사업자번호가 10자리가 아니면 에러 발생', function () {
            Livewire::test(CreateModal::class)
                ->set('name', fake()->company)
                ->set('owner', fake()->name)
                ->set('business_register_number', '123456789')
                ->set('address', fake()->address)
                ->set('phone_number', '02' . fake()->numerify('########'))
                ->call('save')
                ->assertHasErrors(['business_register_number' => 'digits'])
                ->assertSee('10자리 사업자번호를 입력해 주세요');
        });

        it('숫자외에 다른 문자열이 입력되면 에러 발생', function () {
            Livewire::test(CreateModal::class)
                ->set('name', fake()->company)
                ->set('owner', fake()->name)
                ->set('business_register_number', 'ABCDEFGHIJ')
                ->set('address', fake()->address)
                ->set('phone_number', '02' . fake()->numerify('########'))
                ->call('save')
                ->assertHasErrors(['business_register_number' => 'numeric'])
                ->assertSee('사업자번호를 다시 확인해 주세요');
        });

        it('이미 등록된 사업자번호를 입력하면 에러 발생', function () {
            // 기존 조직 생성
            $existingBusinessNumber = '1234567890';
            Organization::factory()->create([
                'business_register_number' => $existingBusinessNumber
            ]);

            Livewire::test(CreateModal::class)
                ->set('name', fake()->company)
                ->set('owner', fake()->name)
                ->set('business_register_number', $existingBusinessNumber)
                ->set('address', fake()->address)
                ->set('phone_number', '02' . fake()->numerify('########'))
                ->call('save')
                ->assertHasErrors(['business_register_number' => 'unique'])
                ->assertSee('이미 등록된 사업자번호입니다');
        });

        it('유효한 사업자번호를 입력한 경우 검증을 통과', function () {
            Livewire::test(CreateModal::class)
                ->set('name', fake()->company)
                ->set('owner', fake()->name)
                ->set('business_register_number', fake()->unique()->numerify('##########'))
                ->set('address', fake()->address)
                ->set('phone_number', '02' . fake()->numerify('########'))
                ->call('save')
                ->assertHasNoErrors();
        });
    });

    describe('사업장 주소 검증', function () {
        it('주소가 비어있으면 에러 발생', function () {
            Livewire::test(CreateModal::class)
                ->set('name', fake()->company)
                ->set('owner', fake()->name)
                ->set('business_register_number', fake()->unique()->numerify('##########'))
                ->set('address', '')
                ->set('phone_number', '02' . fake()->numerify('########'))
                ->call('save')
                ->assertHasErrors(['address' => 'required'])
                ->assertSee('사업장 주소를 입력해 주세요');
        });

        it('주소 길이가 10자 미만이면 에러 발생', function () {
            Livewire::test(CreateModal::class)
                ->set('name', fake()->company)
                ->set('owner', fake()->name)
                ->set('business_register_number', fake()->unique()->numerify('##########'))
                ->set('address', '123456789')
                ->set('phone_number', '02' . fake()->numerify('########'))
                ->call('save')
                ->assertHasErrors(['address' => 'min'])
                ->assertSee('주소를 자세히 입력해 주세요');
        });

        it('유효한 주소를 입력한 경우 검증을 통과', function () {
            Livewire::test(CreateModal::class)
                ->set('name', fake()->company)
                ->set('owner', fake()->name)
                ->set('business_register_number', fake()->unique()->numerify('##########'))
                ->set('address', fake()->address)
                ->set('phone_number', '02' . fake()->numerify('########'))
                ->call('save')
                ->assertHasNoErrors();
        });
    });

    describe('사업장 전화번호 검증', function () {
        it('전화번호가 비어있으면 에러 발생', function () {
            Livewire::test(CreateModal::class)
                ->set('name', fake()->company)
                ->set('owner', fake()->name)
                ->set('business_register_number', fake()->unique()->numerify('##########'))
                ->set('address', fake()->address)
                ->set('phone_number', '')
                ->call('save')
                ->assertHasErrors(['phone_number' => 'required'])
                ->assertSee('사업자 전화번호를 입력해 주세요');
        });

        it('전화번호가 0으로 시작하지 않으면 에러 발생', function () {
            Livewire::test(CreateModal::class)
                ->set('name', fake()->company)
                ->set('owner', fake()->name)
                ->set('business_register_number', fake()->unique()->numerify('##########'))
                ->set('address', fake()->address)
                ->set('phone_number', '123456789')
                ->call('save')
                ->assertHasErrors(['phone_number' => 'starts_with'])
                ->assertSee('전화번호를 다시 확인해 주세요');
        });

        it('전화번호가 8자리면 에러 발생', function () {
            Livewire::test(CreateModal::class)
                ->set('name', fake()->company)
                ->set('owner', fake()->name)
                ->set('business_register_number', fake()->unique()->numerify('##########'))
                ->set('address', fake()->address)
                ->set('phone_number', '01234567')
                ->call('save')
                ->assertHasErrors(['phone_number' => 'digits_between'])
                ->assertSee('전화번호를 다시 확인해 주세요');
        });

        it('전화번호가 11자리면 에러 발생', function () {
            Livewire::test(CreateModal::class)
                ->set('name', fake()->company)
                ->set('owner', fake()->name)
                ->set('business_register_number', fake()->unique()->numerify('##########'))
                ->set('address', fake()->address)
                ->set('phone_number', '01234567890')
                ->call('save')
                ->assertHasErrors(['phone_number' => 'digits_between'])
                ->assertSee('전화번호를 다시 확인해 주세요');
        });

        it('숫자외에 다른 문자열이 입력되면 에러 발생', function () {
            Livewire::test(CreateModal::class)
                ->set('name', fake()->company)
                ->set('owner', fake()->name)
                ->set('business_register_number', fake()->unique()->numerify('##########'))
                ->set('address', fake()->address)
                ->set('phone_number', '0ABCDEFGHI')
                ->call('save')
                ->assertHasErrors(['phone_number' => 'numeric'])
                ->assertSee('전화번호를 다시 확인해 주세요');
        });

        it('유효한 전화번호를 입력한 경우 검증 통과', function () {
            Livewire::test(CreateModal::class)
                ->set('name', fake()->company)
                ->set('owner', fake()->name)
                ->set('business_register_number', fake()->unique()->numerify('##########'))
                ->set('address', fake()->address)
                ->set('phone_number', '02' . fake()->numerify('########'))
                ->call('save')
                ->assertHasNoErrors();
        });
    });
});

describe('주소 검색 결과 반영 테스트', function () {
    it('주소 데이터 결과 전달되면 반영되어야 함', function () {
        $addressData = [
            'postcode' => '12345',
            'address'  => '서울특별시 강남구 테헤란로 123',
        ];

        Livewire::test(CreateModal::class)
            ->dispatch('address-selected', $addressData)
            ->assertSet('postcode', '12345')
            ->assertSet('address', '서울특별시 강남구 테헤란로 123')
            ->assertSeeHtml('wire:model="postcode"')
            ->assertSeeHtml('wire:model="address"')
            ->assertSeeHtml('value="12345"')
            ->assertSeeHtml('value="서울특별시 강남구 테헤란로 123"');
    });

    it('빈 주소 데이터 결과가 전달되면 빈 값으로 반영되어야 함', function () {
        $component = Livewire::test(CreateModal::class);

        // 초기값 설정
        $component->set('postcode', '')
            ->set('address', '');

        // 빈 배열로 이벤트 디스패치
        $component->dispatch('address-selected', [])
            ->assertSet('postcode', '')
            ->assertSet('address', '');
    });

    it('주소 데이터 값이 null일 경우, 빈 값으로 반영되어야 함', function () {
        $component = Livewire::test(CreateModal::class);

        // 초기값 설정
        $component->set('postcode', '')
            ->set('address', '');

        // null로 이벤트 디스패치
        $component->dispatch('address-selected', null)
            ->assertSet('postcode', '')
            ->assertSet('address', '');
    });

    it('주소 데이터 값에 address 값이 누락되었을 경우, 빈 값을 반영되어야 함', function () {
        $addressData = [
            'postcode' => '54321',
            // address 필드 누락
        ];

        $component = Livewire::test(CreateModal::class);

        // 초기값 설정
        $component->set('postcode', '')
            ->set('address', '');

        // 부분 데이터로 이벤트 디스패치
        $component->dispatch('address-selected', $addressData)
            ->assertSet('postcode', '')
            ->assertSet('address', ''); // address는 변경되지 않음
    });

    it('주소 데이터 형식이 배열이 아닌 경우, 빈 값으로 반영되어야 함', function () {
        $component = Livewire::test(CreateModal::class);

        // 초기값 설정
        $component->set('postcode', '')
            ->set('address', '');

        // 문자열로 이벤트 디스패치 (배열이 아님)
        $component->dispatch('address-selected', 'not_an_array')
            ->assertSet('postcode', '')
            ->assertSet('address', '');
    });
});
