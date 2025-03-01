<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Testing\Presentation;

use Serendipity\Presentation\Input;

final class HealthInput extends Input
{
    public function rules(): array
    {
        return [
            'message' => 'sometimes|string',
        ];
    }
}
