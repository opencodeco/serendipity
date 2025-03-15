<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Exception;

use Exception;
use Hyperf\HttpMessage\Server\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Serendipity\Domain\Exception\Type;
use Serendipity\Hyperf\Exception\GeneralExceptionHandler;
use Serendipity\Infrastructure\Exception\Thrown;
use Serendipity\Infrastructure\Exception\ThrownFactory;
use Serendipity\Infrastructure\Http\JsonFormatter;

/**
 * @internal
 */
final class GeneralExceptionHandlerTest extends TestCase
{
    private LoggerInterface $logger;

    private GeneralExceptionHandler $handler;

    private ThrownFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logger = $this->createMock(LoggerInterface::class);
        $this->factory = $this->createMock(ThrownFactory::class);
        $this->handler = new GeneralExceptionHandler(
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
                $this->stringContains('<general>'),
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
                $this->stringContains('<general>'),
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
