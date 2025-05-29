<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter\Deserialize;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Serendipity\Domain\Contract\Exportable;
use Serendipity\Domain\Type\Timestamp;
use Serendipity\Infrastructure\Adapter\Deserialize\Demolisher;

final class DemolisherDateTest extends TestCase
{
    public function testShouldDemolishWithTimestamp(): void
    {
        // Arrange
        $demolisher = new Demolisher();
        $timestamp = new Timestamp();
        $instance = new class($timestamp) implements Exportable {
            public function __construct(
                private Timestamp $createdAt
            ) {
            }

            public function export(): array
            {
                return [
                    'createdAt' => $this->createdAt,
                ];
            }
        };

        // Act
        $values = $demolisher->demolish($instance);

        // Assert
        $this->assertEquals($timestamp->toString(), $values->created_at);
    }

    public function testShouldDemolishWithDateTimeImmutable(): void
    {
        // Arrange
        $demolisher = new Demolisher();
        $dateTime = new DateTimeImmutable();
        $instance = new class($dateTime) implements Exportable {
            public function __construct(
                private DateTimeImmutable $updatedAt
            ) {
            }

            public function export(): array
            {
                return [
                    'updatedAt' => $this->updatedAt,
                ];
            }
        };

        // Act
        $values = $demolisher->demolish($instance);

        // Assert
        $this->assertEquals($dateTime->format(DateTimeInterface::ATOM), $values->updated_at);
    }
}
