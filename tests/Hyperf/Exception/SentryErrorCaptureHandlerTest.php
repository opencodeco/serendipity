<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Exception;

use Exception;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Support\MessageBag;
use Hyperf\Validation\ValidationException;
use Hyperf\Validation\Validator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;
use Serendipity\Domain\Exception\InvalidInputException;
use Serendipity\Hyperf\Exception\SentryErrorCaptureHandler;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;

/**
 * Test-specific subclass that exposes protected methods for testing
 */
class TestSentryErrorCaptureHandler extends SentryErrorCaptureHandler
{
    public function publicExtractContext(Throwable $throwable): array
    {
        return $this->extractContext($throwable);
    }

    public function publicHeaders(): array
    {
        return $this->headers();
    }
}

class SentryErrorCaptureHandlerTest extends TestCase
{
    private RequestInterface|MockObject $request;
    private TestSentryErrorCaptureHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = $this->createMock(RequestInterface::class);
        $this->handler = new TestSentryErrorCaptureHandler(
            $this->createMock(\Psr\Log\LoggerInterface::class),
            $this->createMock(\Serendipity\Infrastructure\Http\JsonFormatter::class),
            $this->createMock(\Serendipity\Infrastructure\Exception\ThrownFactory::class),
            $this->request
        );
    }

    public function testIsValidAlwaysReturnsTrue(): void
    {
        // Arrange
        $throwable = $this->createMock(Throwable::class);

        // Act
        $result = $this->handler->isValid($throwable);

        // Assert
        $this->assertTrue($result);
    }

    public function testHandleConfiguresScopeAndCapturesException(): void
    {
        // Arrange
        $throwable = new Exception('Test error message');
        $response = $this->createMock(ResponsePlusInterface::class);

        // Mock the request methods used in extractContext
        $this->request->expects($this->once())
            ->method('post')
            ->willReturn(['test' => 'value']);

        $this->request->expects($this->once())
            ->method('query')
            ->willReturn(['query' => 'param']);

        $this->request->expects($this->once())
            ->method('getHeaders')
            ->willReturn(['Content-Type' => ['application/json']]);

        $this->request->expects($this->once())
            ->method('getMethod')
            ->willReturn('GET');

        $uri = $this->createMock(UriInterface::class);
        $uri->method('__toString')->willReturn('/test/path');

        $this->request->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        // We need to use runkit or similar to mock static functions in real code
        // For this test, we'll just verify the response is returned as-is

        // Act
        $result = $this->handler->handle($throwable, $response);

        // Assert
        $this->assertSame($response, $result);
    }

    public function testExtractContextWithValidationException(): void
    {
        // Arrange
        $validator = $this->createMock(Validator::class);
        $validator->expects($this->once())
            ->method('errors')
            ->willReturn(new MessageBag(['field' => ['Error message']]));

        $exception = $this->createMock(ValidationException::class);
        $exception->validator = $validator;

        $this->request->expects($this->once())
            ->method('post')
            ->willReturn(['test' => 'value']);

        $this->request->expects($this->once())
            ->method('query')
            ->willReturn(['query' => 'param']);

        $this->request->expects($this->once())
            ->method('getHeaders')
            ->willReturn(['Content-Type' => ['application/json']]);

        $this->request->expects($this->once())
            ->method('getMethod')
            ->willReturn('GET');

        $uri = $this->createMock(UriInterface::class);
        $uri->method('__toString')->willReturn('/test/path');

        $this->request->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        // Act
        $result = $this->handler->publicExtractContext($exception);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('body', $result);
        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('headers', $result);
        $this->assertArrayHasKey('query', $result);
        $this->assertArrayHasKey('request', $result);
        $this->assertEquals(['field' => ['Error message']], $result['errors']);
    }

    public function testExtractContextWithInvalidInputException(): void
    {
        // Arrange
        $errors = ['field' => 'Invalid input'];
        $exception = $this->createMock(InvalidInputException::class);
        $exception->expects($this->once())
            ->method('getErrors')
            ->willReturn($errors);

        $this->request->expects($this->once())
            ->method('post')
            ->willReturn(['test' => 'value']);

        $this->request->expects($this->once())
            ->method('query')
            ->willReturn(['query' => 'param']);

        $this->request->expects($this->once())
            ->method('getHeaders')
            ->willReturn(['Content-Type' => ['application/json']]);

        $this->request->expects($this->once())
            ->method('getMethod')
            ->willReturn('GET');

        $uri = $this->createMock(UriInterface::class);
        $uri->method('__toString')->willReturn('/test/path');

        $this->request->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        // Act
        $result = $this->handler->publicExtractContext($exception);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('errors', $result);
        $this->assertEquals($errors, $result['errors']);
    }

    public function testExtractContextWithGenericException(): void
    {
        // Arrange
        $exception = $this->createMock(Exception::class);

        $this->request->expects($this->once())
            ->method('post')
            ->willReturn(['test' => 'value']);

        $this->request->expects($this->once())
            ->method('query')
            ->willReturn(['query' => 'param']);

        $this->request->expects($this->once())
            ->method('getHeaders')
            ->willReturn(['Content-Type' => ['application/json']]);

        $this->request->expects($this->once())
            ->method('getMethod')
            ->willReturn('GET');

        $uri = $this->createMock(UriInterface::class);
        $uri->method('__toString')->willReturn('/test/path');

        $this->request->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        // Act
        $result = $this->handler->publicExtractContext($exception);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('errors', $result);
        $this->assertEquals([], $result['errors']);
    }

    public function testHeaders(): void
    {
        // Arrange
        $headers = [
            'Content-Type' => ['application/json'],
            'Accept' => ['application/json', 'text/html'],
            'X-Single-Value' => 'single',
            'X-Numeric-Value' => 123,
        ];

        $this->request->expects($this->once())
            ->method('getHeaders')
            ->willReturn($headers);

        // Act
        $result = $this->handler->publicHeaders();

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals('application/json', $result['Content-Type']);
        $this->assertEquals('application/json; text/html', $result['Accept']);
        $this->assertEquals('single', $result['X-Single-Value']);
        $this->assertEquals('123', $result['X-Numeric-Value']);
    }
}
