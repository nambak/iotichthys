<?php

namespace App\Listeners;

use App\Events\OrganizationCreating;
use App\Models\Organization;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

class GenerateSlug
{
    private const MAX_RETRY_ATTEMPTS = 5;

    public function __construct(
        private LoggerInterface $logger,
    ) {}

    /**
     * Handle the event.
     *
     * @param OrganizationCreating $event
     * @return void
     */
    public function handle(OrganizationCreating $event): void
    {
        $organization = $event->organization;

        // slug가 비어있으면 name으로부터 자동 생성
        if (empty($organization->slug)) {
            $organization->slug = $this->generateUniqueSlugWithRetry($organization->name);
        }
    }

    /**
     * 재시도 로직을 포함한 고유한 slug 생성
     *
     * @param string $name
     * @return string
     * @throws \RuntimeException
     */
    private function generateUniqueSlugWithRetry(string $name): string
    {
        // Application 인스턴스에서 locale 가져오기
        $locale = app()->getLocale();
        $baseSlug = Str::slug($name, '-', $locale);

        for ($attempt = 0; $attempt < self::MAX_RETRY_ATTEMPTS; $attempt++) {
            try {
                $slug = $this->generateSlugCandidate($baseSlug, $attempt);

                // DB에서 중복 확인 (최종 검증용)
                if (!$this->slugExists($slug)) {
                    return $slug;
                }
            } catch (QueryException $e) {
                // DB 연결 문제 등으로 실패한 경우 로깅 후 재시도
                $this->logger->warning('Slug uniqueness check failed', [
                    'attempt' => $attempt + 1,
                    'slug'    => $slug ?? $baseSlug,
                    'error'   => $e->getMessage()
                ]);

                if ($attempt === self::MAX_RETRY_ATTEMPTS - 1) {
                    throw new \RuntimeException(
                        "Failed to generate unique slug after " . self::MAX_RETRY_ATTEMPTS . " attempts"
                    );
                }
            }
        }

        throw new \RuntimeException("Failed to generate unique slug for name: {$name}");
    }

    /**
     * 시도 횟수에 따른 slug 후보 생성
     *
     * @param string $baseSlug
     * @param int $attempt
     * @return string
     */
    private function generateSlugCandidate(string $baseSlug, int $attempt): string
    {
        if ($attempt === 0) {
            return $baseSlug;
        }

        // 첫 번째 재시도에서는 현재 시각의 마이크로초를 추가
        if ($attempt === 1) {
            return $baseSlug . '-' . substr((string)(microtime(true) * 10000), -6);
        }

        // 그 이후에는 랜덤 문자열 추가
        return $baseSlug . '-' . Str::random(6);
    }

    /**
     * slug가 이미 존재하는지 확인
     *
     * @param string $slug
     * @return bool
     */
    private function slugExists(string $slug): bool
    {
        return Organization::where('slug', $slug)->exists();
    }
}
