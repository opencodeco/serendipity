<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Deserialize;

use Serendipity\Domain\Contract\Message;
use Serendipity\Infrastructure\Adapter\Deserialize\Resolve\ConverterChain;
use Serendipity\Infrastructure\Adapter\Deserialize\Resolve\DependencyChain;
use Serendipity\Infrastructure\Adapter\Deserialize\Resolve\DoNothingChain;
use Serendipity\Infrastructure\Adapter\Serialize\Engine;

use function get_object_vars;

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
            $name = $this->name($field);

            $resolved = (new DoNothingChain($this->case))
                ->then(new DependencyChain($this->case))
                ->then(new ConverterChain($this->case, $this->converters))
                ->resolve($value);

            $data[$name] = $resolved->content;
        }
        return $data;
    }

    public function extractValues(object $instance): array
    {
        if ($instance instanceof Message) {
            return $instance->values()?->toArray() ?? [];
        }
        return get_object_vars($instance);
    }
}
