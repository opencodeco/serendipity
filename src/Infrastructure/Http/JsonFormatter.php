<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Http;

use InvalidArgumentException;
use Serendipity\Domain\Contract\Formatter;
use Serendipity\Infrastructure\Exception\Type;
use Throwable;

use function json_encode;
use function Serendipity\Type\Cast\stringify;
use function sprintf;

class JsonFormatter implements Formatter
{
    public function format(mixed $value, mixed $option = null): string
    {
        try {
            $type = $this->type($option);
            $parsed = $this->parse($value, $type);
            return json_encode($parsed, JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            return sprintf('{"status":"error","message":"%s"}', addslashes($exception->getMessage()));
        }
    }

    private function type(mixed $option): ?Type
    {
        if ($option === null || $option instanceof Type) {
            return $option;
        }
        throw new InvalidArgumentException(sprintf("The 'option' must be an instance of '%s'.", Type::class));
    }

    private function parse(mixed $value, ?Type $type = null): array
    {
        if ($type === null) {
            return [
                'status' => 'success',
                'data' => $value,
            ];
        }
        return match ($type) {
            Type::INVALID_INPUT => [
                'status' => 'fail',
                'data' => $value,
            ],
            Type::UNTREATED => [
                'status' => 'error',
                'message' => stringify($value),
            ],
        };
    }
}
