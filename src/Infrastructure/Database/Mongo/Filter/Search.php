<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Database\Mongo\Filter;

use Serendipity\Infrastructure\Database\Mongo\Filter\Condition\BetweenCondition;
use Serendipity\Infrastructure\Database\Mongo\Filter\Condition\EqualCondition;
use Serendipity\Infrastructure\Database\Mongo\Filter\Condition\InCondition;
use Serendipity\Infrastructure\Database\Mongo\Filter\Condition\RegexCondition;

use function Serendipity\Type\Cast\stringify;

final class Search extends SearchEngine
{
    /**
     * @param array<string,Condition|string> $conditions
     */
    public static function create(
        array $conditions = [
            'between' => BetweenCondition::class,
            'equal' => EqualCondition::class,
            'in' => InCondition::class,
            'regex' => RegexCondition::class,
        ],
    ): self {
        return new self(new ConditionParser($conditions));
    }

    public function make(string $field, mixed $value): string
    {
        $value = stringify($value);
        return implode(self::SEARCH_SEPARATOR, [$field, sprintf('"%s"', $value)]);
    }

    public function unmake(string $filter): array
    {
        $pieces = explode(self::SEARCH_SEPARATOR, $filter, 2);
        if (count($pieces) === 2) {
            return [$pieces[0] => $this->removeQuotes($pieces[1])];
        }
        return [$pieces[0] => null];
    }

    public function parse(string $expression): array
    {
        $matches = $this->match($expression);

        $stack = [];
        $parsed = [];
        foreach ($matches as $match) {
            if ($this->isKeyValue($match)) {
                $parsed = $this->mergeCondition($parsed, $match['key'], $match['value']);
                continue;
            }
            if (! empty($match['operator'])) {
                $parsed = $this->wrapOperator($parsed, $match['operator']);
                continue;
            }
            $this->handleGrouping($parsed, $stack, $match);
        }
        return $parsed;
    }
}
