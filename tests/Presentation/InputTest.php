<?php

declare(strict_types=1);

namespace Serendipity\Test\Presentation;

use Serendipity\Domain\Exception\InvalidInputException;
use Serendipity\Domain\Support\Set;
use Serendipity\Hyperf\Testing\Extension\InputExtension;
use Serendipity\Hyperf\Testing\Extension\MakeExtension;
use Serendipity\Presentation\Input;
use Serendipity\Test\Testing\ExtensibleCase;
use Serendipity\Testing\Extension\FakerExtension;

/**
 * @internal
 */
final class InputTest extends ExtensibleCase
{
    use MakeExtension;
    use InputExtension;
    use FakerExtension;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpInput();
    }

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
        $this->setUpRequestContext($parsedBody);
        $this->assertEquals('cool', $input->value('datum'));
        $this->assertEquals($parsedBody, $input->values()->toArray());
    }

    public function testShouldGetValueFromValues(): void
    {
        $array = ['datum' => 'cool'];
        /** @var Input $input */
        $input = $this->make(
            Input::class,
            [
                'values' => Set::createFrom($array),
            ]
        );
        $this->assertEquals($array, $input->values()->toArray());
        $this->assertSame($input->content(), $input->values());
    }

    public function testShouldGetPropertyFromHeaders(): void
    {
        $headers = ['header' => 'cool'];
        $input = $this->input(class: Input::class, headers: $headers);
        $this->setUpRequestContext(headers: $headers);
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

        $this->setUpRequestContext(params: $params);
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

    public function testShouldUseMappingsToGetValues(): void
    {
        $mappings = [
            'source.0.field:name' => 'strtoupper',
            'source.1.field:description' => fn (mixed $value) => sprintf('cool: %s', $value),
            'deep.deep.down:not_found' => 'trim',
        ];
        $parsedBody = [
            'source' => [
                [
                    'field' => 'my value',
                ],
                [
                    'field' => 'nice',
                ],
            ],
        ];
        $rules = [
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
        ];
        $args = [
            'rules' => $rules,
            'mappings' => $mappings,
        ];
        $input = $this->input(class: Input::class, parsedBody: $parsedBody, args: $args);

        $actual = $input->value('name');
        $this->assertEquals('MY VALUE', $actual);

        $actual = $input->value('description');
        $this->assertEquals('cool: nice', $actual);
    }

    public function testShouldRaiseErrorOnMappingsSetupMisconfiguration(): void
    {
        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage(
            "Detected 1 errors: \"Mapping left side (setup) must be a 'string', got 'integer'\""
        );

        $mappings = [
            fn (mixed $value) => sprintf('cool: %s', $value),
        ];
        $parsedBody = [
            'source' => [
                0 => [
                    'field' => 'My value',
                ],
            ],
        ];
        $rules = [
            'target' => ['required', 'string'],
        ];
        $args = [
            'rules' => $rules,
            'mappings' => $mappings,
        ];
        $input = $this->input(class: Input::class, parsedBody: $parsedBody, args: $args);
        $input->values();
    }

    public function testShouldRaiseErrorOnMappingsFormatterMisconfiguration(): void
    {
        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage(
            "Detected 1 errors: \"Mapping right side (formatter) must be a 'callable', got 'string'\""
        );

        $mappings = [
            'source.0.field:target' => 'not a callable',
        ];
        $parsedBody = [
            'source' => [
                0 => [
                    'field' => 'My value',
                ],
            ],
        ];
        $rules = [
            'target' => ['required', 'string'],
        ];
        $args = [
            'rules' => $rules,
            'mappings' => $mappings,
        ];
        $input = $this->input(class: Input::class, parsedBody: $parsedBody, args: $args);
        $input->values();
    }
}
