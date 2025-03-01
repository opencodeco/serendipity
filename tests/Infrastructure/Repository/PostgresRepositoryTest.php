<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Repository;

use Exception;
use Hyperf\DB\DB;
use Serendipity\Domain\Exception\GeneratingException;
use Serendipity\Domain\Exception\UniqueKeyViolationException;
use Serendipity\Infrastructure\Adapter\Deserializer;
use Serendipity\Infrastructure\Repository\Factory\HyperfDBFactory;
use Serendipity\Infrastructure\Repository\Generator;
use Serendipity\Infrastructure\Repository\Serializing\RelationalDeserializerFactory;
use Serendipity\Infrastructure\Repository\Serializing\RelationalSerializerFactory;
use Serendipity\Test\TestCase;
use stdClass;

final class PostgresRepositoryTest extends TestCase
{
    private PostgresRepositoryTestMock $repository;

    private Deserializer $deserializer;

    private RelationalDeserializerFactory $deserializerFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $generator = $this->createMock(Generator::class);
        $this->deserializer = $this->createMock(Deserializer::class);

        $this->deserializerFactory = $this->createMock(RelationalDeserializerFactory::class);
        $serializerFactory = $this->createMock(RelationalSerializerFactory::class);

        $hyperfDBFactory = $this->createMock(HyperfDBFactory::class);
        $hyperfDBFactory->expects($this->once())
            ->method('make')
            ->willReturn($this->createMock(DB::class));

        $this->repository = new PostgresRepositoryTestMock(
            $generator,
            $this->deserializerFactory,
            $serializerFactory,
            $hyperfDBFactory,
        );
    }

    public function testShouldGenerateBindings(): void
    {
        $this->deserializerFactory->expects($this->once())
            ->method('make')
            ->with(stdClass::class)
            ->willReturn($this->deserializer);

        $this->deserializer->expects($this->once())
            ->method('deserialize')
            ->willReturn(['field' => 'value']);

        $values = $this->repository->exposeBindings(
            instance: new stdClass(),
            fields: ['field'],
            generate: [
                'cuid' => 'id',
                'at' => 'now',
            ]
        );
        $this->assertEquals(['value'], $values);
    }

    public function testShouldRaiseMappingExceptionOnInvalidGenerate(): void
    {
        $this->expectException(GeneratingException::class);

        $this->repository->exposeBindings(
            instance: new stdClass(),
            fields: ['field'],
            generate: ['field' => 'invalid']
        );
    }


    public function testShouldRenderColumns(): void
    {
        $this->assertEquals(
            '"field_one", "field_two"',
            $this->repository->exposeColumns(['field_one', 'field_two'])
        );
    }


    public function testShouldRenderValues(): void
    {
        $this->assertEquals(
            '?, ?',
            $this->repository->exposeWildcards(['field_one', 'field_two'])
        );
    }

    public function testShouldNotDetectUniqueConstraintViolation(): void
    {
        $result = $this->repository->exposeDetectUniqueKeyViolation(new Exception("It's not a violation"));
        $this->assertNull($result);
    }

    public function testShouldDetectUniqueConstraintViolation(): void
    {
        $message = 'duplicate key value violates unique constraint "baz" DETAIL: Key (foo)=(bar) already exists.';
        $result = $this->repository->exposeDetectUniqueKeyViolation(new Exception($message));
        $this->assertInstanceOf(UniqueKeyViolationException::class, $result);
        $this->assertEquals('bar', $result->value);
        $this->assertEquals('foo', $result->key);
        $this->assertEquals('baz', $result->resource);
    }
}
