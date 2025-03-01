<?php

declare(strict_types=1);

namespace Serendipity\Testing\Example\Game\Presentation\Input;

use Serendipity\Presentation\Input;

class CreateGameInput extends Input
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'slug' => ['required', 'string'],
            'data' => ['sometimes', 'array'],
        ];
    }
}
