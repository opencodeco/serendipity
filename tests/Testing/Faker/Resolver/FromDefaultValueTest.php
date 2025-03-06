<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Faker\Resolver;

use PHPUnit\Framework\TestCase;
use Serendipity\Domain\Support\Reflective\CaseConvention;
use Serendipity\Domain\Support\Reflective\Factory\Target;
use Serendipity\Domain\Support\Set;
use Serendipity\Test\Testing\Stub\Builtin;
use Serendipity\Test\Testing\Stub\Command;
use Serendipity\Test\Testing\Stub\NullableAndOptional;
use Serendipity\Testing\Faker\Resolver\FromDefaultValue;

/**
 * @internal
 */
final class FromDefaultValueTest extends TestCase
{
    public function testShouldResolveParameterWithDefaultValue(): void
    {
        $resolver = new FromDefaultValue(CaseConvention::SNAKE);
        $target = Target::createFrom(NullableAndOptional::class);
        $parameters = $target->getReflectionParameters();

        $this->assertCount(3, $parameters);

        [2 => $optional] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($optional, $set);

        $this->assertNotNull($value);
        $this->assertEquals(10, $value->content);
    }

    public function testShouldResolveOptionalParameter(): void
    {
        $resolver = new FromDefaultValue(CaseConvention::SNAKE);
        $target = Target::createFrom(Command::class);
        $parameters = $target->getReflectionParameters();

        // A classe Command tem vários parâmetros opcionais
        [6 => $address] = $parameters; // O parâmetro 'address' é opcional e permite null

        $set = Set::createFrom([]);
        $value = $resolver->resolve($address, $set);

        $this->assertNotNull($value);
        $this->assertNull($value->content);
    }

    public function testShouldResolveNullableParameter(): void
    {
        $resolver = new FromDefaultValue(CaseConvention::SNAKE);
        $target = Target::createFrom(NullableAndOptional::class);
        $parameters = $target->getReflectionParameters();

        $this->assertCount(3, $parameters);

        [0 => $nullable] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($nullable, $set);

        $this->assertNotNull($value);
        $this->assertNull($value->content);
    }

    public function testShouldFallbackToNextResolver(): void
    {
        $resolver = new FromDefaultValue(CaseConvention::SNAKE);
        $target = Target::createFrom(Builtin::class);
        $parameters = $target->getReflectionParameters();

        $this->assertCount(6, $parameters);

        [0 => $string] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($string, $set);

        $this->assertNull($value);
    }
}
