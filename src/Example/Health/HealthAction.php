<?php

declare(strict_types=1);

namespace Serendipity\Example\Health;

use Psr\Log\LoggerInterface;

readonly class HealthAction
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function __invoke(HealthInput $input): string
    {
        $value = $input->value('message', 'Kicking ass and taking names!');
        $this->logger->emergency(sprintf('Health action message using emergency: %s', $value));
        $this->logger->alert(sprintf('Health action message using alert: %s', $value));
        $this->logger->critical(sprintf('Health action message using critical: %s', $value));
        $this->logger->error(sprintf('Health action message using error: %s', $value));
        $this->logger->warning(sprintf('Health action message using warning: %s', $value));
        $this->logger->notice(sprintf('Health action message using notice: %s', $value));
        $this->logger->info(sprintf('Health action message using info: %s', $value));
        $this->logger->debug(sprintf('Health action message using debug: %s', $value));
        return $value;
    }
}
