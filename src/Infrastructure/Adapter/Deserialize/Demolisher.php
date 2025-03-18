<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Deserialize;

use ReflectionException;
use Serendipity\Domain\Contract\Exportable;
use Serendipity\Domain\Contract\Message;
use Serendipity\Domain\Support\Reflective\Engine;
use Serendipity\Domain\Support\Reflective\Factory\Target;
use Serendipity\Domain\Support\Set;
use Serendipity\Infrastructure\Adapter\Deserialize\Resolve\AttributeChain;
use Serendipity\Infrastructure\Adapter\Deserialize\Resolve\DependencyChain;
use Serendipity\Infrastructure\Adapter\Deserialize\Resolve\DoNothingChain;
use Serendipity\Infrastructure\Adapter\Deserialize\Resolve\FormatterChain;

use function get_object_vars;
use function Serendipity\Type\Cast\arrayify;

class Demolisher extends Engine
{
    /**
     * @return array<string, mixed>
     * @throws ReflectionException
     */
    public function demolish(object $instance): array
    {
        $target = Target::createFrom($instance::class);
        $parameters = $target->getReflectionParameters();
        if (empty($parameters)) {
            return [];
        }

        $parameters = $target->getReflectionParameters();
        $set = Set::createFrom($this->extractValues($instance));
        $data = [];
        foreach ($parameters as $parameter) {
            $name = $parameter->getName();
            if (! $set->has($name)) {
                continue;
            }

            $resolved = (new DoNothingChain($this->case))
                ->then(new DependencyChain($this->case))
                ->then(new AttributeChain($this->case))
                ->then(new FormatterChain($this->case, $this->formatters))
                ->resolve($parameter, $set->get($name));

            $field = $this->formatParameterName($parameter);
            $data[$field] = $resolved->content;
        }
        return $data;
    }

    public function extractValues(object $instance): array
    {
        if ($instance instanceof Message) {
            return arrayify($instance->content());
        }
        if ($instance instanceof Exportable) {
            return (array) $instance->export();
        }
        return get_object_vars($instance);
    }
}
