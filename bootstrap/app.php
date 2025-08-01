<?php

use App\Http\Middleware\CheckPermission;
use App\Providers\EventServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        EventServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'permission', CheckPermission::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // 개발환경이 아닐 때만 Sentry 통합 활성화
        if (env('APP_ENV') !== 'local') {
            Integration::handles($exceptions);
        }
    })->create();
