<?php

declare(strict_types=1);

namespace Serendipity\Test\Presentation;

use Serendipity\Domain\Support\Set;
use Serendipity\Presentation\Input;
use Serendipity\Test\TestCase;

final class InputTest extends TestCase
{
    public function testShouldAuthorize(): void
    {
        $input = $this->make(Input::class, ['authorize' => false]);

        $this->assertFalse($input->authorize());
    }

    public function testShouldHasGivenRules(): void
    {
        $input = $this->make(
            Input::class,
            [
                'rules' => [
                    'test' => ['sometimes', 'string'],
                ],
            ]
        );
        $rules = $input->rules();

        $this->assertArrayHasKey('test', $rules);
        $this->assertEquals(['sometimes', 'string'], $rules['test']);
    }

    public function testShouldGetValueFromParsedBody(): void
    {
        $parsedBody = ['datum' => 'cool'];
        $input = $this->make(
            Input::class,
            [
                'rules' => [
                    'datum' => 'required|string',
                ],
            ]
        );
        $this->setUpRequest($parsedBody);
        $this->assertEquals('cool', $input->value('datum'));
        $this->assertEquals($parsedBody, $input->values()->toArray());
    }

    public function testShouldGetValueFromValues(): void
    {
        $array = ['datum' => 'cool'];
        $input = $this->make(
            Input::class,
            [
                'values' => Set::createFrom($array),
            ]
        );
        $this->assertEquals($array, $input->values()->toArray());
    }

    public function testShouldGetPropertyFromHeaders(): void
    {
        $headers = ['header' => 'cool'];
        $input = $this->input(class: Input::class, headers: $headers);
        $this->setUpRequest(headers: $headers);
        $this->assertEquals('cool', $input->property('header'));
        $this->assertEquals($headers, $input->properties()->toArray());
    }

    public function testShouldGetPropertyFromProperties(): void
    {
        $array = ['header' => 'cool'];
        $input = $this->make(
            Input::class,
            [
                'properties' => Set::createFrom($array),
            ]
        );
        $this->assertEquals($array, $input->properties()->toArray());
    }

    public function testShouldGetValueFromParams(): void
    {
        $param = $this->generator()->uuid();
        $params = ['param' => $param];
        $input = $this->make(
            Input::class,
            [
                'rules' => [
                    'param' => 'required|string',
                ],
            ]
        );

        $this->setUpRequest(params: $params);
        $this->assertEquals($param, $input->value('param'));
    }

    public function testShouldCallValueBehindPost(): void
    {
        $input = $this->make(Input::class, ['values' => Set::createFrom(['test' => 'cool'])]);
        $this->assertEquals('cool', $input->post('test'));
    }

    public function testShouldCallValuesBehindPost(): void
    {
        $input = $this->make(Input::class, ['values' => Set::createFrom(['test' => 'cool'])]);
        $this->assertEquals(['test' => 'cool'], $input->post());
    }

    public function testShouldCallValueBehindInput(): void
    {
        $input = $this->make(Input::class, ['values' => Set::createFrom(['test' => 'cool'])]);
        $this->assertEquals('cool', $input->input('test'));
    }
}
