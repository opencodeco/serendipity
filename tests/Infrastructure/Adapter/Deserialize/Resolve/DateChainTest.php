<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter\Deserialize\Resolve;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;
use Serendipity\Domain\Type\Timestamp;
use Serendipity\Infrastructure\Adapter\Deserialize\Chain;
use Serendipity\Infrastructure\Adapter\Deserialize\Resolve\DateChain;
use Serendipity\Domain\Support\Value;

final class DateChainTest extends TestCase
{
    public function testResolveWithTimestamp(): void
    {
        // Arrange
        $chain = new DateChain();
        $parameter = $this->createMock(ReflectionParameter::class);
        $timestamp = new Timestamp();

        // Act
        $result = $chain->resolve($parameter, $timestamp);

        // Assert
        $this->assertEquals($timestamp->toString(), $result->content);
    }

    public function testResolveWithDateTimeInterface(): void
    {
        // Arrange
        $chain = new DateChain();
        $parameter = $this->createMock(ReflectionParameter::class);
        $dateTime = new DateTimeImmutable();

        // Act
        $result = $chain->resolve($parameter, $dateTime);

        // Assert
        $this->assertEquals($dateTime->format(DateTimeInterface::ATOM), $result->content);
    }

    public function testResolveWithNonDateValue(): void
    {
        // Arrange
        $value = 'not a date';
        $parameter = $this->createMock(ReflectionParameter::class);

        // Create a test double of DateChain that extends the real DateChain
        $chain = new class extends DateChain {
            public function resolveByClassName(mixed $value): ?string
            {
                // This will always return null for our test, forcing it to call parent::resolve
                return null;
            }
        };

        // Act
        $result = $chain->resolve($parameter, $value);

        // Assert
        $this->assertEquals($value, $result->content);
    }
}
