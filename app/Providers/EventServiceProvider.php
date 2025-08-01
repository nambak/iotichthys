<?php

namespace App\Providers;

use App\Events\OrganizationCreating;
use App\Events\PermissionCreating;
use App\Events\PermissionUpdating;
use App\Events\TeamCreating;
use App\Listeners\GenerateSlug;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        OrganizationCreating::class => [
            GenerateSlug::class,
        ],
        TeamCreating::class => [
            GenerateSlug::class,
        ],
        PermissionCreating::class => [
            GenerateSlug::class,
        ],
        PermissionUpdating::class => [
            GenerateSlug::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}