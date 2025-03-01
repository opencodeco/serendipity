<?php

declare(strict_types=1);

namespace Serendipity\Presentation;

use Hyperf\Context\Context;
use Hyperf\Validation\Request\FormRequest;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Serendipity\Domain\Contract\Message;
use Serendipity\Domain\Support\Set;

use function array_keys;
use function array_merge;
use function Hyperf\Collection\data_get;

/**
 * @see https://hyperf.wiki/3.1/#/en/validation?id=form-request-validation
 */
class Input extends FormRequest implements Message
{
    public function __construct(
        ContainerInterface $container,
        private readonly Set $properties = new Set([]),
        private readonly Set $values = new Set([]),
        private readonly array $rules = [],
        private readonly bool $authorize = true,
    ) {
        parent::__construct($container);
    }

    public function authorize(): bool
    {
        return $this->authorize;
    }

    public function rules(): array
    {
        return $this->rules;
    }

    final public function properties(): Set
    {
        if (Context::has(ServerRequestInterface::class)) {
            $headers = $this->getHeaders();
            $headers = $this->normalizeHeaders($headers);
            return $this->properties->along($headers);
        }
        return $this->properties;
    }

    final public function property(string $key, mixed $default = null): ?string
    {
        return data_get($this->properties()->toArray(), $key, $default);
    }

    /**
     * @SuppressWarnings(StaticAccess)
     */
    final public function values(): Set
    {
        if (Context::has(ServerRequestInterface::class)) {
            return $this->values->along($this->validated());
        }
        return $this->values;
    }

    /**
     * @template T of mixed
     * @param T $default
     *
     * @return T
     */
    final public function value(string $key, mixed $default = null): mixed
    {
        return data_get($this->values()->toArray(), $key, $default);
    }

    /**
     * @deprecated Use `value(string $key, mixed $default = null): mixed` instead
     */
    final public function post(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->values()->toArray();
        }
        return $this->value($key, $default);
    }

    /**
     * @deprecated Use `value(string $key, mixed $default = null): mixed` instead
     */
    final public function input(string $key, mixed $default = null): mixed
    {
        return $this->value($key, $default);
    }

    protected function validationData(): array
    {
        $data = parent::validationData();
        $params = $this->extractParams($data);
        return array_merge($data, $params);
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

    private function normalizeHeaders(array $headers): array
    {
        return array_map(
            fn (mixed $value) => is_array($value) ? implode('; ', $value) : $value,
            $headers
        );
    }
}
