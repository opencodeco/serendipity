<?php

declare(strict_types=1);

namespace Serendipity\Presentation;

use Psr\Container\ContainerInterface;
use Serendipity\Domain\Contract\Message;
use Serendipity\Domain\Exception\InvalidInputException;
use Serendipity\Domain\Support\Set;
use Serendipity\Hyperf\Request\HyperfFormRequest;

use function array_keys;
use function array_merge;
use function Hyperf\Collection\data_get;
use function Hyperf\Collection\data_set;
use function Serendipity\Type\Cast\toString;

/**
 * @see https://hyperf.wiki/3.1/#/en/validation?id=form-request-validation
 */
class Input extends HyperfFormRequest implements Message
{
    public function __construct(
        ContainerInterface $container,
        Set $properties = new Set([]),
        Set $values = new Set([]),
        /**
         * @var array<string, array|string>
         */
        protected readonly array $rules = [],
        /**
         * @var array<string, callable(mixed $value): mixed>
         */
        protected readonly array $mappings = [],
        protected readonly bool $authorize = true,
    ) {
        parent::__construct($container, $properties, $values);
    }

    public function authorize(): bool
    {
        return $this->authorize;
    }

    /**
     * @return array<string, array|string>
     */
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
     * @return array<string, callable(mixed $value): mixed>
     */
    protected function mappings(): array
    {
        return $this->mappings;
    }

    /**
     * @throws InvalidInputException
     */
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

    /**
     * @SuppressWarnings(CyclomaticComplexity)
     * @throws InvalidInputException
     */
    private function extractMapped(array $data): array
    {
        $mappings = $this->mappings();
        $errors = [];
        $mapped = [];
        foreach ($mappings as $setup => $formatter) {
            $detected = $this->detectMisconfiguration($setup, $formatter);
            if ($detected) {
                $errors[toString($setup)] = $detected;
                continue;
            }
            [$from, $target] = $this->extractMappedFromAndTarget($setup);
            $previous = data_get($data, $from);
            if ($previous === null) {
                continue;
            }
            $value = $formatter($previous);
            data_set($mapped, $target, $value);
        }

        if (empty($errors)) {
            /* @phpstan-ignore return.type */
            return $mapped;
        }
        throw new InvalidInputException($errors);
    }

    /**
     * @param string $setup
     * @return array<string>
     */
    private function extractMappedFromAndTarget(string $setup): array
    {
        $pieces = explode(':', $setup);
        $from = $pieces[0];
        $target = $pieces[1] ?? $from;
        return [toString($from), toString($target)];
    }

    /**
     * @SuppressWarnings(CyclomaticComplexity)
     */
    private function detectMisconfiguration(mixed $setup, mixed $formatter): ?string
    {
        $isString = is_string($setup);
        if ($isString && is_callable($formatter)) {
            return null;
        }

        $format = $isString
            ? "Mapping right side (formatter) must be a 'callable', got '%s'"
            : "Mapping left side (setup) must be a 'string', got '%s'";
        $value = $isString ? gettype($formatter) : gettype($setup);
        return sprintf($format, $value);
    }
}
