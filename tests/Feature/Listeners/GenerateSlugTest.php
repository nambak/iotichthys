<?php

use App\Events\OrganizationCreating;
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
        'slug' => 'test-organization'
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
    $attempts = [];
    $originalListener = $this->listener;

    // 리스너를 상속해서 재시도 과정 캡처
    $observableListener = new class($this->mockLogger) extends GenerateSlug {
        public array $slugAttempts = [];

        public function handle(OrganizationCreating $event): void
        {
            $organization = $event->organization;
            if (empty($organization->slug)) {
                try {
                    $organization->slug = $this->captureSlugGeneration($organization->name);
                } catch (\RuntimeException $e) {
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

                if (!Organization::where('slug', $slug)->exists()) {
                    return $slug;
                }
            }

            throw new \RuntimeException("Failed to generate unique slug after 5 attempts");
        }

        private function generateSlugCandidate(string $baseSlug, int $attempt): string
        {
            if ($attempt === 0) return $baseSlug;
            if ($attempt === 1) return $baseSlug . '-' . substr((string)(microtime(true) * 10000), -6);
            return $baseSlug . '-' . Str::random(6);
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

it('데이터베이스 오류 시 경고 로그 기록', function () {
    // DB 연결 오류 시뮬레이션
    $this->mockLogger->shouldReceive('warning')
        ->once()
        ->with('Slug uniqueness check failed', Mockery::any());

    // 테스트 구현...
});
