<?php

declare(strict_types=1);

namespace Serendipity\Testing\Faker\Resolver;

use ReflectionParameter;
use Serendipity\Domain\Exception\GeneratingException;
use Serendipity\Domain\Support\Reflective\Attributes\Define;
use Serendipity\Domain\Support\Reflective\Attributes\Managed;
use Serendipity\Domain\Support\Reflective\Definition\Type;
use Serendipity\Domain\Support\Reflective\Definition\TypeExtended;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;
use Serendipity\Hyperf\Testing\Extension\MakeExtension;
use Serendipity\Testing\Extension\ManagedExtension;
use Serendipity\Testing\Faker\Resolver;

final class FromTypeAttributes extends Resolver
{
    use MakeExtension;
    use ManagedExtension;

    /**
     * @throws GeneratingException
     */
    public function resolve(ReflectionParameter $parameter, Set $presets): ?Value
    {
        $type = $this->extractType($parameter);
        if ($type === null) {
            return parent::resolve($parameter, $presets);
        }

        return $this->resolveByAttributes($parameter)
            ?? parent::resolve($parameter, $presets);
    }

    /**
     * @throws GeneratingException
     */
    private function resolveByAttributes(ReflectionParameter $parameter): ?Value
    {
        $attributes = $parameter->getAttributes();
        if (empty($attributes)) {
            return null;
        }
        foreach ($attributes as $attribute) {
            $instance = $attribute->newInstance();
            $resolved = match (true) {
                $instance instanceof Managed => $this->resolveManaged($instance),
                $instance instanceof Define => $this->resolveDefine($instance),
                default => null,
            };
            if ($resolved !== null) {
                return $resolved;
            }
        }
        return null;
    }

    /**
     * @throws GeneratingException
     */
    private function resolveManaged(Managed $instance): ?Value
    {
        return match ($instance->management) {
            'id' => new Value($this->managed()->id()),
            'now' => new Value($this->managed()->now()),
            default => null,
        };
    }

    private function resolveDefine(Define $instance): ?Value
    {
        $types = $instance->types;
        $callback = fn (?Value $carry, Type|TypeExtended $type) => $carry ?? match (true) {
            $type instanceof Type => $this->resolveByFormat($type->value),
            $type instanceof TypeExtended => $type->fake($this),
        };
        return array_reduce($types, $callback);
    }

    private function resolveByFormat(string $type): Value
    {
        return new Value($this->generator->format($type));
    }
}
