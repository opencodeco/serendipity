<?php

declare(strict_types=1);

namespace Serendipity\Test\Domain\Type;

use DateTimeImmutable;
use DateTimeInterface;
use JsonSerializable;
use PHPUnit\Framework\TestCase;
use Serendipity\Domain\Type\Timestamp;

final class TimestampTest extends TestCase
{
    public function testTimestampExtendsDateTimeImmutable(): void
    {
        // Arrange & Act
        $timestamp = new Timestamp();

        // Assert
        $this->assertInstanceOf(DateTimeImmutable::class, $timestamp);
        $this->assertInstanceOf(JsonSerializable::class, $timestamp);
    }

    public function testToString(): void
    {
        // Arrange
        $timestamp = new Timestamp();
        $expected = $timestamp->format(DateTimeInterface::ATOM);

        // Act
        $result = $timestamp->toString();

        // Assert
        $this->assertEquals($expected, $result);
    }

    public function testJsonSerialize(): void
    {
        // Arrange
        $timestamp = new Timestamp();
        $expected = $timestamp->toString();

        // Act
        $result = $timestamp->jsonSerialize();

        // Assert
        $this->assertEquals($expected, $result);
    }

    public function testCreateFromString(): void
    {
        // Arrange
        $dateString = '2023-05-19T12:00:00+00:00';

        // Act
        $timestamp = new Timestamp($dateString);

        // Assert
        $this->assertEquals($dateString, $timestamp->toString());
    }

    public function testImmutability(): void
    {
        // Arrange
        $timestamp = new Timestamp();

        // Act
        $newTimestamp = $timestamp->modify('+1 day');

        // Assert
        $this->assertNotSame($timestamp, $newTimestamp);
        $this->assertInstanceOf(Timestamp::class, $newTimestamp);
    }
}
