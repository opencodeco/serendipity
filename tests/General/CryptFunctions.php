<?php

declare(strict_types=1);

namespace Serendipity\Test\General;

use PHPUnit\Framework\TestCase;

use function Serendipity\Crypt\decrypt;
use function Serendipity\Crypt\encrypt;

/**
 * @internal
 */
final class CryptFunctions extends TestCase
{
    public function testShouldEncrypt(): void
    {
        $encrypted = encrypt('test');
        $this->assertIsString($encrypted);
        $this->assertNotEquals('test', $encrypted);
        $this->assertJson(base64_decode($encrypted));
        $this->assertEquals('test', decrypt($encrypted));
    }
}
