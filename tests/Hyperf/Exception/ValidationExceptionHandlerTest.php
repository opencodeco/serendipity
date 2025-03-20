<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Exception;

use Hyperf\Contract\MessageBag;
use Hyperf\Contract\ValidatorInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Validation\ValidationException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Serendipity\Domain\Exception\InvalidInputException;
use Serendipity\Hyperf\Exception\ValidationExceptionHandler;
use Serendipity\Infrastructure\Http\JsonFormatter;
use Swow\Psr7\Message\ResponsePlusInterface;
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
        $request = $this->createMock(RequestInterface::class);
        $handler = new ValidationExceptionHandler($logger, $formatter, $request);

        $response = $this->createMock(ResponsePlusInterface::class);
        $response->method('setStatus')->willReturnSelf();
        $response->method('addHeader')->willReturnSelf();
        $response->method('setBody')->willReturnSelf();

        $messageBag = $this->createMock(MessageBag::class);
        $messageBag->method('getMessages')
            ->willReturn(decode('{"name":["validation.required"],"slug":["validation.required"]}'));

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('errors')
            ->willReturn($messageBag);

        $throwable = new ValidationException($validator);

        $result = $handler->handle($throwable, $response);

        $this->assertInstanceOf(ResponsePlusInterface::class, $result);
    }

    public function testHandleShouldReturnInvalidInputErrors(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $formatter = $this->createMock(JsonFormatter::class);
        $request = $this->createMock(RequestInterface::class);
        $handler = new ValidationExceptionHandler($logger, $formatter, $request);

        $response = $this->createMock(ResponsePlusInterface::class);
        $response->method('setStatus')->willReturnSelf();
        $response->method('addHeader')->willReturnSelf();
        $response->method('setBody')->willReturnSelf();

        $throwable = new InvalidInputException([
            'source.[0].field:target' => "Mapping right side (formatter) must be a \'callable\', got \'%s\'",
        ]);

        $result = $handler->handle($throwable, $response);

        $this->assertInstanceOf(ResponsePlusInterface::class, $result);
    }

    public function testIsValidShouldReturnTrueForValidationException(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $formatter = $this->createMock(JsonFormatter::class);
        $request = $this->createMock(RequestInterface::class);
        $handler = new ValidationExceptionHandler($logger, $formatter, $request);

        $validator = $this->createMock(ValidatorInterface::class);
        $throwable = new ValidationException($validator);

        $this->assertTrue($handler->isValid($throwable));
    }

    public function testIsValidShouldReturnFalseForNonValidationException(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $formatter = $this->createMock(JsonFormatter::class);
        $request = $this->createMock(RequestInterface::class);
        $handler = new ValidationExceptionHandler($logger, $formatter, $request);
        $throwable = $this->createMock(Throwable::class);

        $this->assertFalse($handler->isValid($throwable));
    }

    public function testHandleShouldHandleOtherExceptions(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $formatter = $this->createMock(JsonFormatter::class);
        $request = $this->createMock(RequestInterface::class);
        $handler = new ValidationExceptionHandler($logger, $formatter, $request);

        $response = $this->createMock(ResponsePlusInterface::class);
        $response->method('setStatus')->willReturnSelf();
        $response->method('addHeader')->willReturnSelf();
        $response->method('setBody')->willReturnSelf();

        $throwable = new RuntimeException('Generic exception');

        $result = $handler->handle($throwable, $response);

        $this->assertInstanceOf(ResponsePlusInterface::class, $result);
    }
}
