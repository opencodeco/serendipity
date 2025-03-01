<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Http\Exception;

use Exception;
use Hyperf\HttpMessage\Server\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Serendipity\Infrastructure\Exception\Thrown;
use Serendipity\Infrastructure\Exception\ThrownFactory;
use Serendipity\Infrastructure\Exception\Type;
use Serendipity\Infrastructure\Http\Exception\AppExceptionHandler;
use Serendipity\Infrastructure\Http\Formatter\JsonFormatter;
use Serendipity\Test\TestCase;

final class AppExceptionHandlerTest extends TestCase
{
    private LoggerInterface $logger;

    private AppExceptionHandler $handler;

    private ThrownFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logger = $this->createMock(LoggerInterface::class);
        $this->factory = $this->createMock(ThrownFactory::class);
        $this->handler = new AppExceptionHandler(
            $this->logger,
            $this->factory,
            new JsonFormatter()
        );
    }

    public function testHandleShouldLogErrorAndReturnFormattedResponseWith500Code(): void
    {
        $this->logger->expects($this->once())
            ->method('alert')
            ->with(
                $this->stringContains('[AppExceptionHandler]'),
                $this->arrayHasKey('message')
            );

        $throwable = new Exception('Test Exception');
        $response = new Response();

        $this->factory->expects($this->once())
            ->method('make')
            ->with($throwable)
            ->willReturn(Thrown::createFrom($throwable));

        $result = $this->handler->handle($throwable, $response);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertEquals(500, $result->getStatusCode());
        $this->assertJson((string) $result->getBody());
    }

    public function testHandleShouldLogErrorAndReturnFormattedResponseWith404Code(): void
    {
        $this->logger->expects($this->once())
            ->method('notice')
            ->with(
                $this->stringContains('[AppExceptionHandler]'),
                $this->arrayHasKey('message')
            );

        $throwable = new Exception('Test Exception', 404);
        $response = new Response();

        $this->factory->expects($this->once())
            ->method('make')
            ->with($throwable)
            ->willReturn(Thrown::createFrom($throwable, Type::INVALID_INPUT));

        $result = $this->handler->handle($throwable, $response);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertEquals(404, $result->getStatusCode());
        $this->assertJson((string) $result->getBody());
    }

    public function testIsValidShouldAlwaysReturnIfExceptionIsNotIgnored(): void
    {
        $throwable = new Exception('Test Exception');
        $this->assertTrue($this->handler->isValid($throwable));
    }
}
