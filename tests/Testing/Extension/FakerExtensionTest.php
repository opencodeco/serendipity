<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Extension;

use Faker\Generator;
use PHPUnit\Framework\TestCase;
use Serendipity\Testing\Faker\Faker;
use Serendipity\Testing\Mock\FakerExtensionMock;

final class FakerExtensionTest extends TestCase
{
    public function testFaker(): void
    {
        $mock = new FakerExtensionMock(
            $this->createMock(Faker::class),
            fn (string $actual) => $this->assertEquals(Faker::class, $actual),
        );
        $mock->assertFaker();
    }

    public function testGenerator(): void
    {
        $faker = $this->createMock(Faker::class);
        $mock = new FakerExtensionMock($faker);
        $faker->expects($this->once())
            ->method('generator')
            ->willReturn($this->createMock(Generator::class));

        $mock->assertGenerator();
    }
}
