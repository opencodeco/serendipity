<?php

declare(strict_types=1);

use Serendipity\Presentation\Output\Accepted;
use Serendipity\Presentation\Output\Created;
use Serendipity\Presentation\Output\NoContent;
use Serendipity\Presentation\Output\NotFound;
use Serendipity\Presentation\Output\Ok;

use function Hyperf\Support\env;

return [
    'hosts' => [
        'mockoon' => [
            'base_uri' => env('MOCKOON_URL', 'http://mockoon:3000/api/v1'),
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ],
    ],
    'result' => [
        Ok::class => [
            'status' => 200,
        ],
        Created::class => [
            'status' => 201,
        ],
        Accepted::class => [
            'status' => 202,
        ],
        NoContent::class => [
            'status' => 204,
        ],
        NotFound::class => [
            'status' => 404,
        ],
    ],
];
