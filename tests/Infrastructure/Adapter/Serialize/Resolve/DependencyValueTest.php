<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter\Serialize\Resolve;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Serendipity\Domain\Exception\Adapter\NotResolved;
use Serendipity\Domain\Support\Set;
use Serendipity\Hyperf\Testing\Extension\MakeExtension;
use Serendipity\Infrastructure\Adapter\Serialize\Resolve\DependencyValue;
use Serendipity\Test\Testing\Stub\Builtin;
use Serendipity\Test\Testing\Stub\Command;
use Serendipity\Test\Testing\Stub\Complex;
use Serendipity\Test\Testing\Stub\EntityStub;
use Serendipity\Test\Testing\Stub\Native;
use Serendipity\Testing\Extension\FakerExtension;
use stdClass;

class DependencyValueTest extends TestCase
{
    use MakeExtension;
    use FakerExtension;

    public function testShouldHandleDependency(): void
    {
        $chain = new DependencyValue();
        $target = $chain->target(Command::class);
        $parameters = $target->parameters;

        $set = Set::createFrom([
            'signup_date' => '2021-01-01',
        ]);

        [
            2 => $signupDate,
            13 => $dob,
        ] = $parameters;

        $resolved = $chain->resolve($signupDate, $set);
        $this->assertInstanceOf(DateTimeImmutable::class, $resolved->content);

        $resolved = $chain->resolve($dob, $set);
        $this->assertInstanceOf(NotResolved::class, $resolved->content);
    }

    public function testShouldHandleDependencyComplex(): void
    {
        $chain = new DependencyValue(path: ['complex']);
        $target = $chain->target(Complex::class);
        $parameters = $target->parameters;

        $generator = $this->generator();
        $set = Set::createFrom([
            'entity' => [
                'id' => 'ss', // $generator->numberBetween(1, 100),
                'price' => $generator->randomFloat(),
                'name' => $generator->name(),
                'is_active' => $generator->boolean(),
            ],
            'native' => [
                'callable' => fn () => null,
                'std_class' => new stdClass(),
                'date_time_immutable' => new DateTimeImmutable(),
                'date_time' => '2021-01-01',
                'date_time_interface' => '2021-01-01',
            ],
            'builtin' => [
                'string' => $generator->word(),
                'int' => $generator->numberBetween(1, 100),
                'bool' => $generator->boolean(),
                'array' => $generator->words(),
                'null' => null,
            ],
        ]);

        [
            $entity,
            $native,
            $builtin,
        ] = $parameters;

        $resolved = $chain->resolve($entity, $set);
        $this->assertInstanceOf(EntityStub::class, $resolved->content);

        $resolved = $chain->resolve($native, $set);
        $this->assertInstanceOf(Native::class, $resolved->content);

        $resolved = $chain->resolve($builtin, $set);
        $this->assertInstanceOf(Builtin::class, $resolved->content);
    }
}
