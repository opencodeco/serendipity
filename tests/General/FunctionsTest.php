<?php

declare(strict_types=1);

namespace Serendipity\Test\General;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class FunctionsTest extends TestCase
{
    public function testShouldRequireFunctions(): void
    {
        $files = [
            'src/@functions/cast.php',
            'src/@functions/crypt.php',
            'src/@functions/json.php',
            'src/@functions/polyfill.php',
            'src/@functions/runtime.php',
            'src/@functions/string.php',
            'src/@functions/util.php',
        ];
        foreach ($files as $file) {
            $filename = __DIR__ . '/../../' . $file;
            $this->assertFileExists($filename, sprintf("File '%s' does not exist", $file));
            require $filename;
        }
    }
}
