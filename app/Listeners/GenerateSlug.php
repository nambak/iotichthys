<?php

namespace App\Listeners;

use App\Events\OrganizationCreating;
use App\Events\TeamCreating;
use App\Models\Organization;
use App\Models\Team;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;
use RuntimeException;

class GenerateSlug
{
    private const MAX_RETRY_ATTEMPTS = 5;
    private const MAX_SLUG_LENGTH = 255;
    private const SUFFIX_RESERVED_LENGTH = 20; // suffix를 위한 예약 공간

    public function __construct(
        private LoggerInterface $logger,
    ) {}

    /**
     * Handle OrganizationCreating event.
     *
     * @param OrganizationCreating $event
     * @return void
     */
    public function handle(OrganizationCreating|TeamCreating $event): void
    {
        $model = match(true) {
            $event instanceof OrganizationCreating => $event->organization,
            $event instanceof TeamCreating => $event->team,
        };

        // slug가 비어있으면 name으로부터 자동 생성
        if (empty($model->slug)) {
            $model->slug = $this->generateUniqueSlugWithRetry($model->name, $model);
        }
    }

    /**
     * 재시도 로직을 포함한 고유한 slug 생성
     *
     * @param string $name
     * @param Model $model
     * @return string
     * @throws \RuntimeException
     */
    private function generateUniqueSlugWithRetry(string $name, Model $model): string
    {
        $locale = app()->getLocale();
        $baseSlug = $this->createTruncatedSlug($name, $locale, $model);

        for ($attempt = 0; $attempt < self::MAX_RETRY_ATTEMPTS; $attempt++) {
            try {
                $slug = $this->generateSlugCandidate($baseSlug, $attempt);

                // DB에서 중복 확인 (최종 검증용)
                if (!$this->slugExists($slug, $model)) {
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
                    throw new RuntimeException(
                        "Failed to generate unique slug after " . self::MAX_RETRY_ATTEMPTS . " attempts"
                    );
                }
            }
        }

        throw new RuntimeException("Failed to generate unique slug for name: $name");
    }

    /**
     * 길이 제한을 고려한 기본 slug 생성
     *
     * @param string $name
     * @param string $locale
     * @param Model $model
     * @return string
     */
    private function createTruncatedSlug(string $name, string $locale, Model $model): string
    {
        // 기본 slug 생성
        $baseSlug = Str::slug($name, '-', $locale);

        // 빈 slug 처리
        if (empty($baseSlug)) {
            $baseSlug = match(get_class($model)) {
                Organization::class => 'organization',
                Team::class => 'team',
                default => 'item'
            };
        }

        // suffix를 위한 공간을 고려하여 길이 제한
        $maxBaseLength = self::MAX_SLUG_LENGTH - self::SUFFIX_RESERVED_LENGTH;

        if (strlen($baseSlug) > $maxBaseLength) {
            // 단어 경계에서 자르기 시도
            $truncated = $this->truncateAtWordBoundary($baseSlug, $maxBaseLength);

            // 단어 경계에서 잘리지 않으면 강제로 자르기
            if (strlen($truncated) > $maxBaseLength) {
                $truncated = substr($baseSlug, 0, $maxBaseLength);
            }

            // 끝에 하이픈이 있으면 제거
            $baseSlug = rtrim($truncated, '-');
        }

        return $baseSlug;
    }

    /**
     * 단어 경계에서 문자열 자르기
     *
     * @param string $text
     * @param int $maxLength
     * @return string
     */
    private function truncateAtWordBoundary(string $text, int $maxLength): string
    {
        if (strlen($text) <= $maxLength) {
            return $text;
        }

        // 최대 길이에서 역방향으로 하이픈 찾기
        $truncated = substr($text, 0, $maxLength);
        $lastHyphenPos = strrpos($truncated, '-');

        // 하이픈을 찾았고, 너무 짧지 않으면 거기서 자르기
        if ($lastHyphenPos !== false && $lastHyphenPos > $maxLength * 0.7) {
            return substr($truncated, 0, $lastHyphenPos);
        }

        return $truncated;
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
     * @param Model $model
     * @return bool
     */
    private function slugExists(string $slug, Model $model): bool
    {
        return match(get_class($model)) {
            Organization::class => Organization::where('slug', $slug)->exists(),
            Team::class => Team::where('slug', $slug)->exists(),
            default => false
        };
    }
}
