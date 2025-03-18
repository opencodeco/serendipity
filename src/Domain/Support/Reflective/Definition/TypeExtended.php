<?php

declare(strict_types=1);

namespace Serendipity\Domain\Support\Reflective\Definition;

use Closure;
use Serendipity\Domain\Contract\Testing\Faker;
use Serendipity\Domain\Support\Value;

interface TypeExtended
{
    public function build(mixed $value, Closure $build): mixed;

    public function demolish(mixed $value): mixed;

    public function fake(Faker $faker): ?Value;
}
