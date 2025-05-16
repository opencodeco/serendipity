<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Event;

use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;

class HttpHandleStarted
{
    public function __construct(public readonly RequestInterface|ServerRequestInterface $request)
    {
    }
}
