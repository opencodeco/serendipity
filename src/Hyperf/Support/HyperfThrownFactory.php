<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Support;

use Hyperf\Contract\ConfigInterface;
use Serendipity\Infrastructure\Exception\ThrownFactory;

class HyperfThrownFactory
{
    public function __construct(private readonly ConfigInterface $config)
    {
    }

    public function make(): ThrownFactory
    {
        $classification = $this->config->get('exception.classification', []);
        return new ThrownFactory($classification);
    }
}
