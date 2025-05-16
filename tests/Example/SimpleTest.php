<?php

declare(strict_types=1);

namespace Serendipity\Test\Example;

use PHPUnit\Framework\TestCase;

final class SimpleTest extends TestCase
{
    public function testSimpleAssertion(): void
    {
        // Arrange
        $expected = true;

        // Act
        $actual = true;

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testStringManipulation(): void
    {
        // Arrange
        $string = 'Serendipity';

        // Act
        $result = strtoupper($string);

        // Assert
        $this->assertEquals('SERENDIPITY', $result);
    }
}
