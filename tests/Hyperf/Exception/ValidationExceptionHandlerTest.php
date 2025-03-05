<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Exception;

use Hyperf\Contract\MessageBag;
use Hyperf\Contract\ValidatorInterface;
use Hyperf\HttpMessage\Server\Response;
use Hyperf\Validation\ValidationException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Serendipity\Domain\Exception\InvalidInputException;
use Serendipity\Hyperf\Exception\ValidationExceptionHandler;
use Serendipity\Infrastructure\Http\JsonFormatter;
use Throwable;

use function Serendipity\Type\Json\decode;

/**
 * @internal
 */
final class ValidationExceptionHandlerTest extends TestCase
{
    public function testHandleShouldReturnValidationErrors(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $formatter = $this->createMock(JsonFormatter::class);
        $handler = new ValidationExceptionHandler($logger, $formatter);
        $response = new Response();

        $messageBag = $this->createMock(MessageBag::class);
        $messageBag->method('getMessages')
            ->willReturn(decode('{"name":["validation.required"],"slug":["validation.required"]}'));

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('errors')
            ->willReturn($messageBag);

        $throwable = new ValidationException($validator);

        $result = $handler->handle($throwable, $response);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertEquals(422, $result->getStatusCode());
    }

    public function testHandleShouldReturnInvalidInputErrors(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $formatter = $this->createMock(JsonFormatter::class);
        $handler = new ValidationExceptionHandler($logger, $formatter);
        $response = new Response();

        $throwable = new InvalidInputException([
            decode('{"source.0.field:target":"Mapping right side (formatter) must be a \'callable\', got \'%s\'"]}'),
        ]);

        $result = $handler->handle($throwable, $response);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertEquals(428, $result->getStatusCode());
    }

    public function testIsValidShouldReturnTrueForValidationException(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $formatter = $this->createMock(JsonFormatter::class);
        $handler = new ValidationExceptionHandler($logger, $formatter);

        $validator = $this->createMock(ValidatorInterface::class);
        $throwable = new ValidationException($validator);

        $this->assertTrue($handler->isValid($throwable));
    }

    public function testIsValidShouldReturnFalseForNonValidationException(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $formatter = $this->createMock(JsonFormatter::class);
        $handler = new ValidationExceptionHandler($logger, $formatter);
        $throwable = $this->createMock(Throwable::class);

        $this->assertFalse($handler->isValid($throwable));
    }
}
