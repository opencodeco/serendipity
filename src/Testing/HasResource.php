<?php

declare(strict_types=1);

namespace Serendipity\Testing;

use Serendipity\Domain\Support\Set;
use Serendipity\Testing\Resource\Helper;
use Throwable;

use function Serendipity\Type\Json\encode;

trait HasResource
{
    /**
     * @var array<string,Helper>
     */
    private array $helpers = [];

    /**
     * @var array<string,string>|null
     */
    private ?array $resources = [];

    protected function helper(string $alias, Helper $helper): void
    {
        $this->helpers[$alias] = $helper;
    }

    protected function resource(string $resource, string $helper): void
    {
        if (isset($this->helpers[$helper])) {
            $this->resources[$resource] = $helper;
        }
        $this->fail('Helper not defined');
    }

    protected function seed(string $type, array $override = [], ?string $resource = null): Set
    {
        $resource = $this->detect($resource);
        $helper = $this->helpers[$resource] ?? null;
        if ($helper instanceof Helper) {
            return $helper->seed($type, $resource, $override);
        }
        $this->fail('Helper not defined');
    }

    protected function tearDownResources(): void
    {
        try {
            foreach ($this->helpers as $resource => $helper) {
                $helper->truncate($resource);
            }
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
        $this->fail('Resource not defined');
    }

    abstract protected function fail(string $string): never;
}
