<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Event;

use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class HttpHandleInterrupted
{
    public function __construct(
        public readonly RequestInterface|ServerRequestInterface $request,
        public readonly Throwable $exception,
    ) {
    }
}
