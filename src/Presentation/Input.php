<?php

declare(strict_types=1);

namespace Serendipity\Presentation;

use Psr\Container\ContainerInterface;
use Serendipity\Domain\Contract\Message;
use Serendipity\Domain\Support\Set;
use Serendipity\Hyperf\Request\HyperfFormRequest;

use function array_keys;
use function array_merge;

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
        return $this->retrieve($this->properties(), $key, $default);
    }

    /**
     * @template T of mixed
     * @param T $default
     *
     * @return T
     */
    final public function value(string $key, mixed $default = null): mixed
    {
        return $this->retrieve($this->values(), $key, $default);
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
}
