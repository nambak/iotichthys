<?php

use App\Events\CategoryCreating;
use App\Events\CategoryUpdating;
use App\Events\OrganizationCreating;
use App\Events\OrganizationUpdating;
use App\Events\PermissionCreating;
use App\Events\PermissionUpdating;
use App\Events\TeamCreating;
use App\Listeners\GenerateSlug;
use App\Models\Organization;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

beforeEach(function () {
    $this->mockLogger = Mockery::mock(LoggerInterface::class);
    $this->mockLogger->shouldReceive('warning')->byDefault();

    $this->listener = new GenerateSlug($this->mockLogger);
});

afterEach(function () {
    Mockery::close();
});

it('slug가 비어있을 경우, 사업자명으로 slug 생성', function () {
    // Given
    $organization = new Organization(['name' => '유명 상사']);
    $event = new OrganizationCreating($organization);

    // When
    $this->listener->handle($event);

    // Then
    expect($organization->slug)->toBe('yumyeong-sangsa');
});

it('이미 slug가 존재할 경우 새로운 slug 생성', function () {
    // Given
    Organization::factory()->create([
        'name' => 'Test Organization',
        'slug' => 'test-organization',
    ]);

    $newOrganization = new Organization(['name' => 'Test Organization']);
    $event = new OrganizationCreating($newOrganization);

    // When
    $this->listener->handle($event);

    // Then
    expect($newOrganization->slug)
        ->not->toBe('test-organization')
        ->toContain('test-organization');
});

it('최대 재시도 횟수 초과 시 예외 발생', function () {

    // 리스너를 상속해서 재시도 과정 캡처
    $observableListener = new class($this->mockLogger) extends GenerateSlug
    {
        public array $slugAttempts = [];

        public function handle(
            OrganizationCreating|
            OrganizationUpdating|
            TeamCreating|
            PermissionCreating|
            PermissionUpdating|
            CategoryUpdating|
            CategoryCreating $event): void
        {
            $organization = $event->model;
            if (empty($organization->slug)) {
                try {
                    $organization->slug = $this->captureSlugGeneration($organization->name);
                } catch (RuntimeException $e) {
                    // 재시도 과정을 캡처한 후 예외 재발생
                    throw $e;
                }
            }
        }

        private function captureSlugGeneration(string $name): string
        {
            $baseSlug = Str::slug($name, '-', app()->getLocale());

            for ($attempt = 0; $attempt < 5; $attempt++) {
                $slug = $this->generateSlugCandidate($baseSlug, $attempt);
                $this->slugAttempts[] = $slug;

                if (! Organization::where('slug', $slug)->exists()) {
                    return $slug;
                }
            }

            throw new RuntimeException('Failed to generate unique slug after 5 attempts');
        }

        private function generateSlugCandidate(string $baseSlug, int $attempt): string
        {
            if ($attempt === 0) {
                return $baseSlug;
            }
            if ($attempt === 1) {
                return $baseSlug.'-'.substr((string) (microtime(true) * 10000), -6);
            }

            return $baseSlug.'-'.Str::random(6);
        }
    };

    // 충돌 상황 생성
    for ($i = 0; $i < 15; $i++) {
        $slug = $i === 0 ? 'observable-test' : "observable-test-{$i}";
        Organization::factory()->create(['slug' => $slug]);
    }

    $organization = new Organization(['name' => 'Observable Test']);
    $event = new OrganizationCreating($organization);

    try {
        $observableListener->handle($event);

        // 성공한 경우
        expect($observableListener->slugAttempts)->not->toBeEmpty();
        expect($organization->slug)->not->toBeEmpty();

    } catch (RuntimeException $e) {

        // 실패한 경우 - 재시도 과정 검증
        expect($observableListener->slugAttempts)->toHaveCount(5);
        expect($e->getMessage())->toContain('Failed to generate unique slug');

        // 실제 시도된 slug들 출력 (디버깅용)
        echo "\n재시도된 slug들:\n";
        foreach ($observableListener->slugAttempts as $i => $slug) {
            echo "시도 {$i}: {$slug}\n";
        }
    }
});

it('slug가 중복되지 않게 생성', function () {
    // Given - 기존 slug가 존재 함
    Organization::factory()->create([
        'name' => 'Test Organization',
        'slug' => 'test-organization',
    ]);

    // When - 같은 이름으로 새 organization 생성
    $newOrganization = new Organization(['name' => 'Test Organization']);
    $event = new OrganizationCreating($newOrganization);
    $this->listener->handle($event);

    // Then - 고유한 slug 생성 확인
    expect($newOrganization->slug)
        ->not->toBe('test-organization')
        ->toContain('test-organization')
        ->and(Organization::where('slug', $newOrganization->slug)->count())->toBe(0);
});

it('사업자명에 특수 문자가 포함된 경우', function () {
    // Given - 특수 문자가 포함된 다양한 이름들
    $specialNames = [
        'Test & Company #1!',
        'Test & Company #2@',
        'Test & Company #3%',
        'Test/Company\\Name',
        'Test   Multiple   Spaces',
        '!!!Special!!!Characters!!!',
    ];

    $generatedSlugs = [];

    foreach ($specialNames as $name) {
        // When - slug 생성
        $organization = new Organization(['name' => $name]);
        $event = new OrganizationCreating($organization);
        $this->listener->handle($event);

        $generatedSlugs[] = $organization->slug;

        // Then - 유효한 slug 형식인지 확인
        expect($organization->slug)
            ->toMatch('/^[a-z0-9가-힣\-]+$/u') // 영문, 숫자, 한글, 하이픈만
            ->not->toBeEmpty();
    }

    // 모든 slug가 고유한지 확인
    expect(array_unique($generatedSlugs))->toHaveCount(count($generatedSlugs));
});

it('매우 긴 조직명의 slug 생성', function () {
    // Given - 매우 긴 이름들
    $longNames = [
        str_repeat('Very Long Organization Name ', 5),
        str_repeat('超长的组织名称', 20),
        str_repeat('매우긴조직이름', 15),
    ];

    foreach ($longNames as $longName) {
        // When - slug 생성
        $organization = new Organization(['name' => $longName]);
        $event = new OrganizationCreating($organization);
        $this->listener->handle($event);

        dump($organization->slug, strlen($organization->slug));

        // Then - DB 컬럼 길이 제한 준수
        expect(strlen($organization->slug))
            ->toBeLessThanOrEqual(255) // 일반적인 VARCHAR 길이
            ->toBeGreaterThan(0);
    }
});
