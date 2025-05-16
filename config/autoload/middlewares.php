<?php

declare(strict_types=1);

use Hyperf\HttpServer\CoreMiddleware;
use Hyperf\Validation\Middleware\ValidationMiddleware;
use Serendipity\Hyperf\Middleware\HttpHandlerMiddleware;

return [
    'http' => [
        ValidationMiddleware::class,
        CoreMiddleware::class => HttpHandlerMiddleware::class,
    ],
];
