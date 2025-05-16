<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Database\Relational\Support;

use Exception;
use PHPUnit\Framework\TestCase;
use Serendipity\Domain\Exception\UniqueKeyViolationException;
use Serendipity\Hyperf\Database\Relational\Support\HasPostgresUniqueConstraint;

class HasPostgresUniqueConstraintTest extends TestCase
{
    use HasPostgresUniqueConstraint;

    public function testShouldNotDetectUniqueConstraintViolation(): void
    {
        $result = $this->detectUniqueKeyViolation(new Exception("It's not a violation"));
        $this->assertNull($result);
    }

    public function testShouldDetectUniqueConstraintViolation(): void
    {
        $message = 'duplicate key value violates unique constraint "baz" DETAIL: Key (foo)=(bar) already exists.';
        $result = $this->detectUniqueKeyViolation(new Exception($message));
        $this->assertInstanceOf(UniqueKeyViolationException::class, $result);
        $this->assertEquals('bar', $result->value);
        $this->assertEquals('foo', $result->key);
        $this->assertEquals('baz', $result->resource);
    }
}
