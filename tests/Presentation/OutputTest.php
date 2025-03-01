<?php

declare(strict_types=1);

namespace Serendipity\Test\Presentation;

use PHPUnit\Framework\TestCase;
use Serendipity\Presentation\Output;

class OutputTest extends TestCase
{
    public function testShouldHasPropertiesAsEmptyArrayAndValuesNull(): void
    {
        $output = Output::createFrom();
        $this->assertEquals([], $output->properties()->toArray());
        $this->assertNull($output->values());
    }

    public function testShouldHasProperties(): void
    {
        $output = Output::createFrom(['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $output->properties()->toArray());
        $this->assertEquals('bar', $output->property('foo'));
    }

    public function testShouldHasValues(): void
    {
        $output = Output::createFrom(values: ['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $output->values()->toArray());
        $this->assertEquals('bar', $output->value('foo'));
    }
}
