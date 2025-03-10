<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Database\Mongo\Filter;

use RuntimeException;

class ConditionParser
{
    public function __construct(
        /**
         * @var array<string,Condition|string>
         */
        private array $conditions = [],
    ) {
    }

    public function parse(string $key, string $content): array
    {
        $content = trim($content, '"');
        $pieces = explode(':', $content, 2);

        if (count($pieces) === 1) {
            return [$key => $content];
        }

        [$alias, $value] = $pieces;
        $negated = false;
        if (str_starts_with($alias, '!')) {
            $negated = true;
            $alias = ltrim($alias, '!');
        }
        $condition = $this->condition($alias);
        $filter = $condition->compose($value);
        if ($negated) {
            return [
                $key => ['$not' => $filter],
            ];
        }
        return [$key => $filter];
    }

    private function condition(string $alias): Condition
    {
        $condition = $this->conditions[$alias] ?? null;
        if ($condition instanceof Condition) {
            return $condition;
        }

        $condition = $this->instance($condition, $alias);
        $this->conditions[$alias] = $condition;
        return $condition;
    }

    private function instance(mixed $condition, string $alias): Condition
    {
        if ($this->isInvalid($condition)) {
            throw new RuntimeException(sprintf("Condition '%s' not found", $alias));
        }
        $instance = new $condition();
        if (! $instance instanceof Condition) {
            throw new RuntimeException(
                sprintf("Condition should be an instance of '%s'", Condition::class)
            );
        }
        return $instance;
    }

    private function isInvalid(mixed $condition): bool
    {
        return ! $condition || ! is_string($condition) || ! class_exists($condition);
    }
}
