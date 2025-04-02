<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Support;

use Hyperf\Contract\ConfigInterface;
use Serendipity\Infrastructure\Exception\ThrownFactory;

use function Serendipity\Type\Cast\arrayify;

class HyperfThrownFactory
{
    public function __construct(private readonly ConfigInterface $config)
    {
    }

    public function make(): ThrownFactory
    {
        $classification = $this->config->get('exceptions.classification', []);
        return new ThrownFactory(arrayify($classification));
    }
}
