<?php

declare(strict_types=1);

namespace Serendipity\Testing;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsTrue;
use Serendipity\Domain\Support\Set;
use Serendipity\Testing\Resource\Helper;
use Throwable;

use function Serendipity\Type\Json\encode;
use function array_key_first;
use function count;
use function sprintf;

/**
 * @phpstan-ignore trait.unused
 */
trait CanAssertResource
{
    /**
     * @var array<string,Helper>
     */
    private array $helpers = [];

    /**
     * @var array<string,string>|null
     */
    private ?array $resources = [];

    protected function setUpHelper(string $alias, Helper $helper): void
    {
        $this->helpers[$alias] = $helper;
    }

    protected function setUpResource(string $resource, string $alias): void
    {
        if (isset($this->helpers[$alias])) {
            $this->resources[$resource] = $alias;
            $helper = $this->helpers[$alias];
            $helper->truncate($resource);
        }
        static::fail('Helper not defined');
    }

    protected function seed(string $type, array $override = [], ?string $resource = null): Set
    {
        $resource = $this->detect($resource);
        $helper = $this->select($resource);
        return $helper->seed($type, $resource, $override);
    }

    protected function tearDownResources(): void
    {
        try {
            foreach ($this->helpers as $resource => $helper) {
                $helper->truncate($resource);
            }
        } catch (Throwable $e) {
            static::fail($e->getMessage());
        }
    }

    protected function assertHas(array $filters, ?string $resource = null): void
    {
        $tallied = $this->tally($resource, $filters);
        $message = sprintf(
            "Expected to find at least one item in resource '%s' with filters '%s'",
            $resource,
            encode($filters),
        );
        static::assertThat($tallied > 0, new IsTrue(), $message);
    }

    protected function assertHasNot(array $filters, ?string $resource = null): void
    {
        $tallied = $this->tally($resource, $filters);
        $message = sprintf(
            "Expected to not find any item in resource '%s' with filters '%s'",
            $resource,
            encode($filters),
        );
        static::assertThat($tallied === 0, new IsTrue(), $message);
    }

    protected function assertHasExactly(int $expected, array $filters, ?string $resource = null): void
    {
        $tallied = $this->tally($resource, $filters);
        $message = sprintf(
            "Expected to find %d items in resource '%s' with filters '%s', but found %d",
            $expected,
            $resource,
            encode($filters),
            $tallied
        );
        static::assertThat($tallied === $expected, new IsTrue(), $message);
    }

    private function tally(string $resource, array $filters): int
    {
        $resource = $this->detect($resource);
        $helper = $this->select($resource);
        return $helper->count($resource, $filters);
    }

    private function select(string $resource): Helper
    {
        $helper = $this->helpers[$resource] ?? null;
        if ($helper instanceof Helper) {
            return $helper;
        }
        static::fail('Helper not defined');
    }

    private function detect(?string $resource): string
    {
        if ($resource !== null) {
            return $resource;
        }
        $resource = $this->resources[$resource] ?? null;
        if ($resource !== null) {
            return $resource;
        }
        if (count($this->resources) === 1) {
            return array_key_first($this->resources);
        }
        static::fail('Resource not defined');
    }

    abstract public static function fail(string $message = ''): never;

    abstract public static function assertThat(mixed $value, Constraint $constraint, string $message = ''): void;

    abstract protected function registerTearDown(callable $callback): void;
}
