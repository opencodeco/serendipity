<?php

declare(strict_types=1);

namespace Serendipity\Testing\Extension;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsTrue;
use Serendipity\Domain\Support\Set;
use Serendipity\Testing\Resource\Helper;

use function array_key_first;
use function count;
use function Serendipity\Type\Json\encode;
use function sprintf;

/**
 * @phpstan-ignore trait.unused
 */
trait ResourceExtension
{
    /**
     * @var array<string,Helper>
     */
    private array $helpers = [];

    /**
     * @var null|array<string,string>
     */
    private ?array $resources = [];

    abstract public static function fail(string $message = ''): never;

    abstract public static function assertThat(mixed $value, Constraint $constraint, string $message = ''): void;

    protected function setUpResourceHelper(string $alias, Helper $helper): void
    {
        $this->helpers[$alias] = $helper;
    }

    protected function setUpResource(string $resource, string $alias): void
    {
        if (isset($this->helpers[$alias])) {
            $this->resources[$resource] = $alias;
            $helper = $this->helpers[$alias];
            $helper->truncate($resource);
            $this->registerTearDown(fn () => $helper->truncate($resource));
            return;
        }
        static::fail('Helper not defined');
    }

    protected function seed(string $type, array $override = [], ?string $resource = null): Set
    {
        $resource = $this->detect($resource);
        $helper = $this->helper($resource);
        return $helper->seed($type, $resource, $override);
    }

    protected function assertHas(array $filters, ?string $resource = null): void
    {
        $resource = $this->detect($resource);
        $tallied = $this->tally($filters, $resource);
        $message = sprintf(
            "Expected to find at least one item in resource '%s' with filters '%s'",
            $resource,
            encode($filters),
        );
        static::assertThat($tallied > 0, new IsTrue(), $message);
    }

    protected function assertHasNot(array $filters, ?string $resource = null): void
    {
        $resource = $this->detect($resource);
        $tallied = $this->tally($filters, $resource);
        $message = sprintf(
            "Expected to not find any item in resource '%s' with filters '%s'",
            $resource,
            encode($filters),
        );
        static::assertThat($tallied === 0, new IsTrue(), $message);
    }

    protected function assertHasExactly(int $expected, array $filters, ?string $resource = null): void
    {
        $resource = $this->detect($resource);
        $tallied = $this->tally($filters, $resource);
        $message = sprintf(
            "Expected to find %d items in resource '%s' with filters '%s', but found %d",
            $expected,
            $resource,
            encode($filters),
            $tallied
        );
        static::assertThat($tallied === $expected, new IsTrue(), $message);
    }

    abstract protected function registerTearDown(callable $callback): void;

    private function tally(array $filters, string $resource): int
    {
        $helper = $this->helper($resource);
        return $helper->count($resource, $filters);
    }

    private function helper(string $resource): Helper
    {
        $alias = $this->resources[$resource] ?? null;
        $helper = $this->helpers[$alias] ?? null;
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
}
