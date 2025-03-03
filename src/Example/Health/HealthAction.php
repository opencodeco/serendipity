<?php

declare(strict_types=1);

namespace Serendipity\Example\Health;

readonly class HealthAction
{
    public function __invoke(HealthInput $input): string
    {
        return $input->value('message', 'Kicking ass and taking names!');
    }
}
