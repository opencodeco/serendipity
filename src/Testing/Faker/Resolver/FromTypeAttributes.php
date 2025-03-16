<?php

declare(strict_types=1);

namespace Serendipity\Testing\Faker\Resolver;

use ReflectionParameter;
use Serendipity\Domain\Exception\ManagedException;
use Serendipity\Domain\Support\Reflective\Attribute\Define;
use Serendipity\Domain\Support\Reflective\Attribute\Managed;
use Serendipity\Domain\Support\Reflective\Attribute\Pattern;
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
     * @throws ManagedException
     */
    public function resolve(ReflectionParameter $parameter, Set $presets): ?Value
    {
        $type = $this->detectReflectionType($parameter->getType());
        if ($type === null) {
            return parent::resolve($parameter, $presets);
        }

        return $this->resolveByAttributes($parameter)
            ?? parent::resolve($parameter, $presets);
    }

    /**
     * @throws ManagedException
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
                $instance instanceof Pattern => $this->resolvePattern($instance),
                default => null,
            };
            if ($resolved !== null) {
                return $resolved;
            }
        }
        return null;
    }

    /**
     * @throws ManagedException
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
        $callback = fn (?Value $carry, Type|TypeExtended $type): ?Value => $carry ?? match (true) {
            $type instanceof Type => $this->resolveByFormat($type->value),
            $type instanceof TypeExtended => $type->fake($this),
        };
        return array_reduce($types, $callback);
    }

    private function resolvePattern(Pattern $instance): Value
    {
        return new Value($this->generator->regexify($instance->pattern));
    }

    private function resolveByFormat(string $type): Value
    {
        return new Value($this->generator->format($type));
    }
}
