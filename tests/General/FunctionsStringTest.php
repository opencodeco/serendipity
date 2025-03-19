<?php

declare(strict_types=1);

namespace Serendipity\Test\General;

use PHPUnit\Framework\TestCase;

use function Serendipity\Type\String\snakify;

/**
 * @internal
 */
final class FunctionsStringTest extends TestCase
{
    public function testSnakifyConvertsUpperCaseToSnakeCase(): void
    {
        $this->assertEquals('camel_case', snakify('camelCase'));
        $this->assertEquals('snake_case', snakify('SnakeCase'));
        $this->assertEquals('multiple_words_here', snakify('MultipleWordsHere'));
    }

    public function testSnakifyHandlesAlreadySnakeCaseStrings(): void
    {
        $this->assertEquals('already_snake', snakify('already_snake'));
    }

    public function testSnakifyHandlesEmptyString(): void
    {
        $this->assertEquals('', snakify(''));
    }

    public function testSnakifyNumber(): void
    {
        $this->assertEquals('super_line_1', snakify('superLine1'));
        $this->assertEquals('line_122', snakify('line122'));
        $this->assertEquals('123_line_122', snakify('123Line122'));
        $this->assertEquals('1_line', snakify('1Line'));

        $this->assertEquals('super_line1', snakify('superLine1', false));
        $this->assertEquals('line122', snakify('line122', false));
        $this->assertEquals('123_line122', snakify('123Line122', false));
        $this->assertEquals('1_line', snakify('1Line', false));
    }
}
