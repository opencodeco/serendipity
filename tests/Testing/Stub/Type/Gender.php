<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Stub\Type;

enum Gender: string
{
    case MALE = 'male';
    case FEMALE = 'female';
}
