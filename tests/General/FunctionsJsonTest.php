<?php

declare(strict_types=1);

namespace Serendipity\Test\General;

use PHPUnit\Framework\TestCase;

use function Serendipity\Type\Json\decode;
use function Serendipity\Type\Json\encode;

final class FunctionsJsonTest extends TestCase
{
    public function testDecodeShouldReturnArrayWhenJsonIsValid(): void
    {
        $json = '{"key": "value", "nested": {"foo": "bar"}}';
        $expected = ['key' => 'value', 'nested' => ['foo' => 'bar']];
        $result = decode($json);
        $this->assertEquals($expected, $result);
    }

    public function testDecodeShouldReturnNullWhenJsonIsInvalid(): void
    {
        $invalidJson = '{key: "value"}'; // Falta aspas em key
        $result = decode($invalidJson);
        $this->assertNull($result);
    }

    public function testDecodeShouldReturnNullWithIncompleteJson(): void
    {
        $incompleteJson = '{"key": "value"';  // Falta o fechamento da chave
        $result = decode($incompleteJson);
        $this->assertNull($result);
    }

    public function testEncodeShouldReturnJsonStringWhenArrayIsValid(): void
    {
        $array = ['key' => 'value', 'nested' => ['foo' => 'bar']];
        $result = encode($array);
        $this->assertJson($result);
        $this->assertEquals('{"key":"value","nested":{"foo":"bar"}}', $result);
    }

    public function testEncodeShouldReturnNullWhenNonUtf8ValuesAreProvided(): void
    {
        // Criar um array com caracteres que causarão uma exceção no json_encode
        $array = ['key' => "\xB1\x31"]; // Caractere inválido em UTF-8
        $result = encode($array);
        $this->assertNull($result);
    }

    public function testEncodeShouldReturnNullWithRecursiveReferences(): void
    {
        // Criar referência recursiva
        $array = [];
        $array['self'] = &$array; // Referência circular
        $result = encode($array);
        $this->assertNull($result);
    }
}
