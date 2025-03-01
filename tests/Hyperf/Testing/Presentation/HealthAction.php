<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Testing\Presentation;

readonly class HealthAction
{
    public function __invoke(HealthInput $input): string
    {
        return $input->value('message', 'Kicking ass and taking names!');
    }
}
