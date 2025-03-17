<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Stub\Type;

use Serendipity\Domain\Contract\Testing\Faker;
use Serendipity\Domain\Support\Reflective\Definition\TypeExtended;
use Serendipity\Domain\Support\Value;

use function Serendipity\Crypt\decrypt;
use function Serendipity\Crypt\encrypt;
use function Serendipity\Type\Cast\stringify;

class Crypt implements TypeExtended
{
    public function build(mixed $value): string
    {
        return decrypt(stringify($value));
    }

    public function demolish(mixed $value): string
    {
        return encrypt(stringify($value));
    }

    public function fake(Faker $faker): ?Value
    {
        return new Value($faker->generate('password'));
    }
}
