<?php

declare(strict_types=1);

namespace Serendipity\Presentation\Input;

use Serendipity\Domain\Exception\InvalidInputException;

use function Serendipity\Type\Cast\toString;

final class Mapped extends Resolver
{
    public function map(array $data): array
    {
        $mappings = $this->input->mappings();
        foreach ($mappings as $target => $value) {
        }
        /*
        $mapped = [];
        $errors = [];
            [$from, $target] = $this->extractMappedFromAndTarget($source);
            $detected = $this->detectMisconfiguration($source, $formatter);
            if ($detected) {
                $errors[toString($source)] = $detected;
                continue;
            }
            $previous = data_get($data, $from);
            if ($previous === null) {
                continue;
            }
            $value = $formatter($previous);
            data_set($mapped, $target, $value);
        */
        if (empty($errors)) {
            /* @phpstan-ignore return.type */
            return $mapped;
        }
        throw new InvalidInputException($errors);
    }

    /**
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
