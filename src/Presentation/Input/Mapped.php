<?php

declare(strict_types=1);

namespace Serendipity\Presentation\Input;

use function Hyperf\Collection\data_get;
use function Hyperf\Collection\data_set;

final class Mapped extends Resolver
{
    public function resolve(array $data): array
    {
        $mappings = $this->input->mappings();
        foreach ($mappings as $target => $from) {
            $value = $this->extractValue($data, $target, $from);
            if ($value === null) {
                continue;
            }
            data_set($data, $target, $value);
        }
        return parent::resolve($data);
    }

    private function extractValue(array $data, string|int $target, mixed $from): mixed
    {
        return match (true) {
            is_string($from) => data_get($data, $from),
            is_callable($from) => $from($data, data_get($data, $target)),
            default => null,
        };
    }
}
