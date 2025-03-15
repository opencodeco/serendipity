<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Http;

use PHPUnit\Framework\TestCase;
use Serendipity\Domain\Exception\Type;
use Serendipity\Infrastructure\Http\JsonFormatter;
use stdClass;

/**
 * @internal
 */
final class JsonFormatterTest extends TestCase
{
    public function testShouldFormatSuccess(): void
    {
        $formatter = new JsonFormatter();
        $formatted = $formatter->format(new stdClass());
        $this->assertEquals('{"status":"success","data":{}}', $formatted);
    }

    public function testShouldFormatFail(): void
    {
        $formatter = new JsonFormatter();
        $formatted = $formatter->format([], Type::INVALID_INPUT);
        $this->assertEquals('{"status":"fail","data":[]}', $formatted);
    }

    public function testShouldFormatError(): void
    {
        $formatter = new JsonFormatter();
        $formatted = $formatter->format(null, Type::UNTREATED);
        $this->assertEquals('{"status":"error","message":""}', $formatted);
    }

    public function testShouldThrowsExceptionWhenTypeIsInvalid(): void
    {
        $formatter = new JsonFormatter();
        $formatted = $formatter->format(null, 200);
        $message = addslashes(sprintf("The 'option' must be an instance of '%s'.", Type::class));
        $this->assertEquals(sprintf('{"status":"error","message":"%s"}', $message), $formatted);
    }
}
