<?php

declare(strict_types=1);


use Hyperf\HttpServer\Exception\Handler\HttpExceptionHandler;
use Serendipity\Domain\Exception\ThrowableType;
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
    'classification' => [
        RuntimeException::class => ThrowableType::UNTREATED,
    ],
];
