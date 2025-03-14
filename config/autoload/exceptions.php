<?php

declare(strict_types=1);


use Hyperf\HttpServer\Exception\Handler\HttpExceptionHandler;
use Serendipity\Hyperf\Exception\GeneralExceptionHandler;
use Serendipity\Hyperf\Exception\ValidationExceptionHandler;

return [
    'handler' => [
        'http' => [
            ValidationExceptionHandler::class,
            HttpExceptionHandler::class,
            GeneralExceptionHandler::class,
        ],
    ],
];
