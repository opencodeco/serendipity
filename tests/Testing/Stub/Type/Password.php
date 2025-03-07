<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Stub\Type;

use Serendipity\Domain\Contract\Testing\Faker;
use Serendipity\Domain\Support\Reflective\Definition\TypeExtended;
use Serendipity\Domain\Support\Value;

use function Serendipity\Type\Cast\stringify;

class Password implements TypeExtended
{
    public function build(mixed $value): string
    {
        return stringify($value);
    }

    public function demolish(mixed $value): string
    {
        return stringify($value);
    }

    public function fake(Faker $faker): ?Value
    {
        return new Value($faker->generate('password'));
    }
}
