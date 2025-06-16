<?php

namespace App\Listeners;

use App\Events\OrganizationCreating;
use App\Models\Organization;
use Illuminate\Support\Str;

class GenerateSlug
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

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
            $organization->slug = $this->generateUniqueSlug($organization->name);
        }
    }

    /**
     * Organization을 위한 고유한 slug 생성
     *
     * @param string $name
     * @return string
     */
    private function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name, '-', config('app.locale'));
        $slug = $baseSlug;
        $counter = 1;

        // 중복된 slug가 있는지 확인하고, 있으면 숫자를 붙여 고유하게 만듦
        while ($this->slugExists($slug)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
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
