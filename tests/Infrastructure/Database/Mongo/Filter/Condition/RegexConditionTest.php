<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Database\Mongo\Filter\Condition;

use PHPUnit\Framework\TestCase;
use Serendipity\Infrastructure\Database\Mongo\Filter\Condition\RegexCondition;

class RegexConditionTest extends TestCase
{
    public function testShouldComposeOneItem(): void
    {
        $condition = new RegexCondition();
        $composed = $condition->compose('A');
        $this->assertEquals(['$regex' => 'A'], $composed);
    }

    public function testShouldComposeMultipleItem(): void
    {
        $condition = new RegexCondition();
        $composed = $condition->compose('A,B,C');
        $this->assertEquals([
            '$or' => [
                ['$regex' => 'A'],
                ['$regex' => 'B'],
                ['$regex' => 'C'],
            ],
        ], $composed);
    }
}
