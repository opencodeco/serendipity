<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Deserialize;

use Serendipity\Domain\Contract\Exportable;
use Serendipity\Domain\Contract\Message;
use Serendipity\Domain\Support\Meta\Engine;
use Serendipity\Infrastructure\Adapter\Deserialize\Resolve\DependencyChain;
use Serendipity\Infrastructure\Adapter\Deserialize\Resolve\DoNothingChain;
use Serendipity\Infrastructure\Adapter\Deserialize\Resolve\FormatterChain;

use function get_object_vars;
use function Serendipity\Type\Cast\toArray;

class Demolisher extends Engine
{
    /**
     * @return array<string, mixed>
     */
    public function demolish(object $instance): array
    {
        $values = $this->extractValues($instance);
        $data = [];
        foreach ($values as $field => $value) {
            $name = $this->formatParameterName($field);

            $resolved = (new DoNothingChain($this->case))
                ->then(new DependencyChain($this->case))
                ->then(new FormatterChain($this->case, $this->formatters))
                ->resolve($value);

            $data[$name] = $resolved->content;
        }
        return $data;
    }

    public function extractValues(object $instance): array
    {
        if ($instance instanceof Message) {
            return toArray($instance->content());
        }
        if ($instance instanceof Exportable) {
            return (array) $instance->export();
        }
        return get_object_vars($instance);
    }
}
