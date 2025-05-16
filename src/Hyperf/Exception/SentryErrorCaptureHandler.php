<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Exception;

use Sentry\State\Scope;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;

use function Sentry\captureException;
use function Sentry\configureScope;

class SentryErrorCaptureHandler extends AbstractExceptionHandler
{
    public function handle(Throwable $throwable, ResponsePlusInterface $response): ResponsePlusInterface
    {
        configureScope(function (Scope $scope) use ($throwable): void {
            $context = $this->extractContext($throwable);
            foreach ($context as $key => $value) {
                $scope->setExtra($key, $value);
            }
            $scope->setExtra('details', $throwable->getMessage());
        });
        captureException($throwable);
        return $response;
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
