<?php

namespace App\Logging;

use Logtail\Monolog\LogtailHandlerBuilder;

class BetterStackLogger
{
    public function __invoke($logger)
    {
        $handler = LogtailHandlerBuilder::withSourceToken(config('logging.betterstack.source_token'))
            ->withEndpoint(config('logging.betterstack.endpoint'))
            ->build();

        $logger->pushHandler($handler);
    }
}