<?php

declare(strict_types=1);

namespace Serendipity\Example\Game\Presentation\Input;

use Serendipity\Presentation\Input;

class CreateGameInput extends Input
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'slug' => ['required', 'string'],
            'published_at' => ['required', 'date'],
            'data' => ['required', 'array'],
            'features' => ['required', 'array'],
            'features.*.name' => ['required', 'string'],
            'features.*.description' => ['required', 'string'],
            'features.*.enabled' => ['required', 'boolean'],
        ];
    }
}
