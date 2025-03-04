<?php

declare(strict_types=1);

namespace Serendipity\Presentation;

use Psr\Container\ContainerInterface;
use Serendipity\Domain\Contract\Message;
use Serendipity\Domain\Support\Set;
use Serendipity\Hyperf\Request\HyperfFormRequest;
use UnexpectedValueException;

use function array_keys;
use function array_merge;
use function Hyperf\Collection\data_get;
use function Hyperf\Collection\data_set;

/**
 * @see https://hyperf.wiki/3.1/#/en/validation?id=form-request-validation
 */
class Input extends HyperfFormRequest implements Message
{
    public function __construct(
        ContainerInterface $container,
        Set $properties = new Set([]),
        Set $values = new Set([]),
        protected readonly array $rules = [],
        protected readonly array $mappings = [],
        protected readonly bool $authorize = true,
    ) {
        parent::__construct($container, $properties, $values);
    }

    public function authorize(): bool
    {
        return $this->authorize;
    }

    public function rules(): array
    {
        return $this->rules;
    }

    final public function property(string $key, mixed $default = null): ?string
    {
        $retrieved = $this->retrieve($this->properties(), $key, $default);
        return is_string($retrieved) ? $retrieved : null;
    }

    public function content(): Set
    {
        return $this->values();
    }

    /**
     * @return array<string, callable>
     */
    protected function mappings(): array
    {
        return $this->mappings;
    }

    protected function validationData(): array
    {
        $data = parent::validationData();
        $params = $this->extractParams($data);
        $mapped = $this->extractMapped($data);
        return array_merge($data, $params, $mapped);
    }

    private function extractParams(array $data): array
    {
        $keys = array_keys($this->rules());
        $params = [];
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                continue;
            }
            $param = $this->route($key);
            if ($param) {
                $params[$key] = $param;
            }
        }
        return $params;
    }

    protected function extractMapped(array $data): array
    {
        $mappings = $this->mappings();
        $mapped = [];
        foreach ($mappings as $setup => $formatter) {
            $this->validateMappingConstraints($setup, $formatter);
            $pieces = explode(':', $setup);
            $from = $pieces[0];
            $target = $pieces[1] ?? $from;
            $previous = data_get($data, $from);
            if ($previous === null) {
                continue;
            }
            $value = $formatter($previous);
            data_set($mapped, $target, $value);
        }
        return $mapped;
    }

    private function validateMappingConstraints(mixed $setup, mixed $formatter): void
    {
        $isString = is_string($setup);
        if (! $isString || ! is_callable($formatter)) {
            $format = $isString
                ? "Mapping right side (formatter) must be a callable, got '%s'"
                : "Mapping left side (setup) must be a string, got '%s'";
            $value = $isString ? gettype($formatter) : gettype($setup);
            throw new UnexpectedValueException(sprintf($format, $value));
        }
    }
}
