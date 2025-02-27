<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter;

use Serendipity\Infrastructure\Adapter\Input;

class InputTestStub extends Input
{
    public function rules(): array
    {
        return [
            'test' => 'sometimes|string',
            'datum' => 'sometimes|string',
            'param' => 'sometimes|string',
        ];
    }
}
