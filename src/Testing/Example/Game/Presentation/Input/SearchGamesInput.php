<?php

declare(strict_types=1);

namespace Serendipity\Testing\Example\Game\Presentation\Input;

use Serendipity\Presentation\Input;

class SearchGamesInput extends Input
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string'],
            'slug' => ['sometimes', 'string'],
        ];
    }
}
