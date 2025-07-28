<?php

use App\Livewire\Teams\CreateModal;
use App\Models\Organization;
use Livewire\Livewire;

describe('teams/create-modal 컴포넌트', function () {
    it('컴포넌트가 정상적으로 렌더링 된다', function () {
        Livewire::test(CreateModal::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.teams.create-modal');
    });
});

describe('팀 생성 유효성 검증 테스트', function () {
    beforeEach(function () {
        $this->organization = Organization::factory()->create();
    });

    describe('조직 선택 검증', function () {
        it('조직이 선택되지 않으면 에러 발생', function () {
            Livewire::test(CreateModal::class)
                ->set('organization_id', '')
                ->set('name', 'Test Team')
                ->set('slug', 'test-team')
                ->call('save')
                ->assertHasErrors(['organization_id' => 'required'])
                ->assertSee('조직을 선택해 주세요');
        });

        it('존재하지 않는 조직 ID를 입력하면 에러 발생', function () {
            Livewire::test(CreateModal::class)
                ->set('organization_id', '99999')
                ->set('name', 'Test Team')
                ->set('slug', 'test-team')
                ->call('save')
                ->assertHasErrors(['organization_id' => 'exists'])
                ->assertSee('유효하지 않은 조직입니다');
        });
    });

    describe('팀명 검증', function () {
        it('팀명이 비어있으면 에러 발생', function () {
            Livewire::test(CreateModal::class)
                ->set('organization_id', $this->organization->id)
                ->set('name', '')
                ->set('slug', 'test-team')
                ->call('save')
                ->assertHasErrors(['name' => 'required'])
                ->assertSee('팀명을 입력해 주세요');
        });

        it('팀명이 2자 미만이면 에러 발생', function () {
            Livewire::test(CreateModal::class)
                ->set('organization_id', $this->organization->id)
                ->set('name', '가')
                ->set('slug', 'a')
                ->call('save')
                ->assertHasErrors(['name' => 'min'])
                ->assertSee('팀명은 최소 2글자 이상 입력해 주세요');
        });

        it('팀명이 50자 초과하면 에러 발생', function () {
            $longName = str_repeat('가', 51);
            Livewire::test(CreateModal::class)
                ->set('organization_id', $this->organization->id)
                ->set('name', $longName)
                ->set('slug', 'test-team')
                ->call('save')
                ->assertHasErrors(['name' => 'max'])
                ->assertSee('팀명은 최대 50글자까지 입력할 수 있습니다');
        });

        it('유효한 팀명을 입력한 경우 검증을 통과', function () {
            Livewire::test(CreateModal::class)
                ->set('organization_id', $this->organization->id)
                ->set('name', 'Test Team')
                ->set('slug', 'test-team')
                ->call('save')
                ->assertHasNoErrors();
        });
    });

    describe('팀 설명 검증', function () {
        it('팀 설명이 비어있어도 검증을 통과', function () {
            Livewire::test(CreateModal::class)
                ->set('organization_id', $this->organization->id)
                ->set('name', 'Test Team')
                ->set('slug', 'test-team')
                ->set('description', '')
                ->call('save')
                ->assertHasNoErrors();
        });

        it('팀 설명이 500자 초과하면 에러 발생', function () {
            $longDescription = str_repeat('가', 501);
            Livewire::test(CreateModal::class)
                ->set('organization_id', $this->organization->id)
                ->set('name', 'Test Team')
                ->set('slug', 'test-team')
                ->set('description', $longDescription)
                ->call('save')
                ->assertHasErrors(['description' => 'max'])
                ->assertSee('팀 설명은 최대 500글자까지 입력할 수 있습니다');
        });

        it('유효한 팀 설명을 입력한 경우 검증을 통과', function () {
            Livewire::test(CreateModal::class)
                ->set('organization_id', $this->organization->id)
                ->set('name', 'Test Team')
                ->set('slug', 'test-team')
                ->set('description', '이것은 테스트 팀입니다.')
                ->call('save')
                ->assertHasNoErrors();
        });
    });
});

describe('팀 생성 기능 테스트', function () {
    it('유효한 데이터로 팀을 생성할 수 있다', function () {
        $organization = Organization::factory()->create();

        Livewire::test(CreateModal::class)
            ->set('organization_id', $organization->id)
            ->set('name', 'Test Team')
            ->set('slug', 'test-team')
            ->set('description', 'Test Description')
            ->call('save');

        $this->assertDatabaseHas('teams', [
            'organization_id' => $organization->id,
            'name' => 'Test Team',
            'slug' => 'test-team',
            'description' => 'Test Description',
        ]);
    });

    it('팀 생성 후 team-created 이벤트가 발생한다', function () {
        $organization = Organization::factory()->create();

        Livewire::test(CreateModal::class)
            ->set('organization_id', $organization->id)
            ->set('name', 'Test Team')
            ->set('slug', 'test-team')
            ->call('save')
            ->assertDispatched('team-created');
    });
});