<?php

declare(strict_types=1);

namespace Serendipity\Example\Game\Presentation\Input;

use Serendipity\Presentation\Input;

class ReadGameInput extends Input
{
    public function rules(): array
    {
        return [
            'id' => 'required|string',
        ];
    }
}
