<?php

declare(strict_types=1);

namespace Serendipity\Domain\Support\Reflective\Factory\Rules;

use BackedEnum;
use ReflectionEnum;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;
use Serendipity\Domain\Support\Reflective\Factory\Chain;
use Serendipity\Domain\Support\Reflective\Factory\Ruleset;

use function Serendipity\Type\Cast\boolify;

class TypeChain extends Chain
{
    /**
     * @var array<string, string>
     */
    private array $supported = [
        'array' => 'array',
        'bool' => 'boolean',
        'int' => 'integer',
        'float' => 'numeric',
        'string' => 'string',
    ];

    /**
     * @throws ReflectionException
     */
    public function resolve(ReflectionParameter $parameter, Ruleset $rules): Ruleset
    {
        $type = $parameter->getType();
        $field = implode('.', $this->path);
        $this->resolveType($type, $rules, $field);

        return parent::resolve($parameter, $rules);
    }

    /**
     * @throws ReflectionException
     */
    private function resolveType(?ReflectionType $type, Ruleset $rules, string $field): ?bool
    {
        return match (true) {
            $type instanceof ReflectionNamedType => $this->resolveTypeReflectionNamedType($type, $rules, $field),
            $type instanceof ReflectionUnionType => $this->resolveTypeReflectionUnionType($type, $rules, $field),
            default => null,
        };
    }

    private function resolveTypeReflectionNamedType(ReflectionNamedType $type, Ruleset $rules, string $field): bool
    {
        $rule = $type->getName();
        if (isset($this->supported[$rule])) {
            return $rules->add($field, $rule);
        }
        return $this->resolveTypeBackedEnum($rule, $field, $rules);
    }

    private function resolveTypeBackedEnum(string $type, string $field, Ruleset $rules): bool
    {
        if (! enum_exists($type)) {
            return false;
        }

        $reflectionEnum = new ReflectionEnum($type);
        if (! $reflectionEnum->isBacked()) {
            return false;
        }

        return $this->resolveTypeBackedEnumCases($reflectionEnum, $rules, $field);
    }

    /**
     * @throws ReflectionException
     */
    private function resolveTypeReflectionUnionType(ReflectionUnionType $type, Ruleset $rules, string $field): bool
    {
        $done = false;
        foreach ($type->getTypes() as $unionType) {
            $done = $this->resolveType($unionType, $rules, $field);
            if ($done) {
                break;
            }
        }
        return boolify($done);
    }

    private function resolveTypeBackedEnumCases(ReflectionEnum $reflectionEnum, Ruleset $rules, string $field): bool
    {
        $cases = [];
        foreach ($reflectionEnum->getCases() as $case) {
            $value = $case->getValue();
            if ($value instanceof BackedEnum) {
                $cases[] = $value->value;
            }
        }

        if (empty($cases)) {
            return false;
        }
        return $rules->add($field, 'in', ...$cases);
    }
}
