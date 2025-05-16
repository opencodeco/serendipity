<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Database\Document;

use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\TestCase;
use Serendipity\Infrastructure\Database\Document\Mongo\Search;

class SearchTest extends TestCase
{
    public function testShouldMakeSearchParam(): void
    {
        $search = Search::create();
        $this->assertEquals('status="open"', $search->make('status', 'open'));
        $this->assertEquals(['status' => 'open'], $search->unmake('status=open'));
        $this->assertEquals(['status' => null], $search->unmake('status'));
    }

    public function testShouldParseSimpleExpression(): void
    {
        $search = Search::create();

        $expression = 'status=open';
        $filters = $search->parse($expression);

        $this->assertEquals(
            [
                'status' => 'open',
            ],
            $filters
        );
    }

    public function testShouldParseComplexExpression(): void
    {
        $search = Search::create();

        $expression = 'type=best and ((status=open or (priority=high and date=2024-11-22)))';
        $filters = $search->parse($expression);

        $this->assertEquals(
            [
                '$and' => [
                    [
                        'type' => 'best',
                    ],
                    [
                        '$and' => [
                            [
                                '$or' => [
                                    [
                                        'status' => 'open',
                                    ],
                                ],
                            ],
                            [
                                '$and' => [
                                    [
                                        'priority' => 'high',
                                    ],
                                    [
                                        'date' => '2024-11-22',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            $filters
        );
    }

    public function testShouldParseConditionsExpression(): void
    {
        $search = Search::create();
        $expression = 'type="in:ruleA,ruleB" '
            . 'and status=equal:open '
            . 'and reference.date="!between:2024-11-16 18:00:00,2024-11-17 07:10:00"';

        $filters = $search->parse($expression);
        $this->assertEquals(
            [
                '$and' => [
                    [
                        '$and' => [
                            [
                                'type' => [
                                    '$in' => [
                                        'ruleA',
                                        'ruleB',
                                    ],
                                ],
                            ],
                            [
                                'status' => [
                                    '$eq' => 'open',
                                ],
                            ],
                        ],
                    ],
                    [
                        'reference.date' => [
                            '$not' => [
                                '$gte' => UTCDateTime::__set_state([
                                    'milliseconds' => '1731780000000',
                                ]),
                                '$lte' => UTCDateTime::__set_state([
                                    'milliseconds' => '1731827400000',
                                ]),
                            ],
                        ],
                    ],
                ],
            ],
            $filters
        );
    }
}
