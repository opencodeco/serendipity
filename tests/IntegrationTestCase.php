<?php

declare(strict_types=1);

namespace Serendipity\Test;

use Serendipity\Domain\Support\Values;
use Serendipity\Infrastructure\Testing\Persistence\Helper;
use Serendipity\Infrastructure\Testing\Persistence\PostgresHelper;
use Serendipity\Infrastructure\Testing\Persistence\SleekDBHelper;
use Throwable;

use function Serendipity\Type\Json\encode;

/**
 * @SuppressWarnings(ExcessiveClassLength)
 */
class IntegrationTestCase extends TestCase
{
    private ?Helper $sleek = null;

    private ?Helper $postgres = null;

    protected ?string $helper = null;

    protected ?string $resource = null;

    protected bool $truncate = true;

    protected function setUp(): void
    {
        parent::setUp();
        match ($this->helper) {
            'sleek' => $this->sleek = $this->make(SleekDBHelper::class),
            'postgres' => $this->postgres = $this->make(PostgresHelper::class),
            default => null,
        };

        $this->truncate();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->truncate();
    }

    protected function seed(string $type, array $override = [], ?string $resource = null): Values
    {
        if ($this->helper === null) {
            $this->fail('Helper not defined. Please define the helper property.');
        }
        $resource ??= $this->resource ?? '';
        if ($resource === '') {
            $this->fail('Resource not defined');
        }
        return match ($this->helper) {
            'sleek' => $this->sleek->seed($type, $resource, $override),
            'postgres' => $this->postgres->seed($type, $resource, $override),
            default => Values::createFrom([]),
        };
    }

    protected function truncate(): void
    {
        if (! $this->truncate) {
            return;
        }
        if ($this->resource === null) {
            return;
        }
        try {
            match ($this->helper) {
                'sleek' => $this->sleek->truncate($this->resource),
                'postgres' => $this->postgres->truncate($this->resource),
                default => null,
            };
        } catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    protected function assertHas(array $filters, ?string $resource = null, ?string $helper = null): void
    {
        $resource ??= $this->resource ?? '';
        $count = $this->countWithHelper($resource, $filters, $helper);
        $message = sprintf(
            "Expected to find at least one item in resource '%s' with filters '%s'",
            $resource,
            encode($filters),
        );
        $this->assertTrue($count > 0, $message);
    }

    protected function assertHasNot(array $filters, ?string $resource = null, ?string $helper = null): void
    {
        $resource ??= $this->resource ?? '';
        $count = $this->countWithHelper($resource, $filters, $helper);
        $message = sprintf(
            "Expected to not find any item in resource '%s' with filters '%s'",
            $resource,
            encode($filters)
        );
        $this->assertSame($count, 0, $message);
    }

    protected function assertHasCount(
        int $expected,
        array $filters,
        ?string $resource = null,
        ?string $helper = null
    ): void {
        $resource ??= $this->resource ?? '';
        $count = $this->countWithHelper($resource, $filters, $helper);
        $message = sprintf(
            "Expected to find %d items in resource '%s' with filters '%s', but found %d",
            $expected,
            $resource,
            encode($filters),
            $count
        );
        $this->assertSame($count, $expected, $message);
    }

    protected function assertIsEmpty(?string $resource = null, ?string $helper = null): void
    {
        $resource ??= $this->resource ?? '';
        $count = $this->countWithHelper($resource, [], $helper);
        $message = sprintf(
            "Expected resource '%s' to be empty, but found %d items",
            $resource,
            $count
        );
        $this->assertSame($count, 0, $message);
    }

    protected function countWithHelper(string $resource, array $filters, ?string $helper = null): int
    {
        $helper ??= $this->helper ?? null;
        if ($helper === null) {
            $this->fail('Helper not defined');
        }
        if ($resource === '') {
            $this->fail('Resource not defined');
        }
        return match ($helper) {
            'sleek' => $this->sleek->count($resource, $filters),
            'postgres' => $this->postgres->count($resource, $filters),
            default => 0,
        };
    }
}
