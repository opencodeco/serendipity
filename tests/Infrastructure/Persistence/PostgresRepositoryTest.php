<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Persistence;

use Serendipity\Infrastructure\Adapter\Serializing\Deserializer;
use Serendipity\Infrastructure\Persistence\Factory\HyperfDBFactory;
use Serendipity\Infrastructure\Persistence\Generator;
use Serendipity\Infrastructure\Persistence\Serializing\RelationalDeserializerFactory;
use Serendipity\Infrastructure\Persistence\Serializing\RelationalSerializerFactory;
use Serendipity\Test\TestCase;
use stdClass;

final class PostgresRepositoryTest extends TestCase
{
    public function testShouldInstantiatePostgresRepository(): void
    {
        $generator = $this->createMock(Generator::class);
        $deserializer = $this->createMock(Deserializer::class);
        $deserializer->expects($this->once())
            ->method('deserialize')
            ->willReturn(['field' => 'value']);
        $deserializerFactory = $this->createMock(RelationalDeserializerFactory::class);
        $deserializerFactory->expects($this->once())
            ->method('make')
            ->with(stdClass::class)
            ->willReturn($deserializer);
        $serializerFactory = $this->createMock(RelationalSerializerFactory::class);
        $hyperfDBFactory = $this->createMock(HyperfDBFactory::class);
        $hyperfDBFactory->expects($this->once())
            ->method('make')
            ->willReturn($this->createMock(\Hyperf\DB\DB::class));
        $repository = new PostgresRepositoryTestMock(
            $generator,
            $deserializerFactory,
            $serializerFactory,
            $hyperfDBFactory,
        );
        $values = $repository->expose(instance: new \stdClass(), fields: ['field'], generate: [
            'cuid' => 'id',
            'at' => 'now'
        ]);
        $this->assertEquals(['value'], $values);
    }

    public function testShouldRaiseMappingExceptionOnInvalidGenerate(): void
    {
        $this->expectException(\Serendipity\Domain\Exception\GeneratingException::class);
        $generator = $this->createMock(Generator::class);
        $deserializerFactory = $this->createMock(RelationalDeserializerFactory::class);
        $serializerFactory = $this->createMock(RelationalSerializerFactory::class);
        $hyperfDBFactory = $this->createMock(HyperfDBFactory::class);
        $hyperfDBFactory->expects($this->once())
            ->method('make')
            ->willReturn($this->createMock(\Hyperf\DB\DB::class));
        $repository = new PostgresRepositoryTestMock(
            $generator,
            $deserializerFactory,
            $serializerFactory,
            $hyperfDBFactory,
        );
        $repository->expose(instance: new \stdClass(), fields: ['field'], generate: [
            'field' => 'invalid'
        ]);
    }
}
