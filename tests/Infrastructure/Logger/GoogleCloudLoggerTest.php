<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Logger;

use Exception;
use Google\Cloud\Logging\Entry;
use Google\Cloud\Logging\Logger;
use PHPUnit\Framework\TestCase;
use Serendipity\Infrastructure\Logging\GoogleCloudLogger;

use function Hyperf\Collection\data_get;

/**
 * @internal
 */
final class GoogleCloudLoggerTest extends TestCase
{
    private Logger $logger;

    private GoogleCloudLogger $googleCloudLogger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logger = $this->createMock(Logger::class);

        $this->googleCloudLogger = new GoogleCloudLogger(
            driver: $this->logger,
            projectId: 'projectId',
            serviceName: 'serviceName',
            env: 'test',
            useCoroutine: false
        );
    }

    public function testShouldLogMessagesCorrectly(): void
    {
        // Arrange
        $message = 'Test Message';
        $context = ['key' => 'value'];

        $this->logger->expects($this->once())
            ->method('write')
            ->with(
                $this->callback(function (Entry $entry) use ($message, $context) {
                    $info = $entry->info();
                    $asserts = [
                        'logName' => 'projects/serviceName/logs/projectId%2Fenv-test',
                        'severity' => 'INFO',
                        'jsonPayload.key' => $context['key'],
                        'jsonPayload.message' => $message,
                        'resource.type' => 'cloud_run_revision',
                        'resource.labels.configuration_name' => 'serviceName',
                        'resource.labels.location' => 'us-central1',
                        'resource.labels.service_name' => 'serviceName',
                        'resource.labels.project_id' => 'projectId',
                    ];
                    foreach ($asserts as $key => $value) {
                        $expected = data_get($info, $key);
                        if ($expected !== $value) {
                            return false;
                        }
                    }
                    return true;
                })
            );

        // Act
        $this->googleCloudLogger->log('info', $message, $context);

        // Assert
        // Nothing more to assert as behavior is validated via `$this->logger->expects()`
    }

    public function testShouldLogEmergencyMessageCorrectly(): void
    {
        // Arrange
        $message = 'Test Message';
        $context = ['key' => 'value'];

        $this->logger->expects($this->once())
            ->method('write')
            ->with($this->isInstanceOf(Entry::class));

        // Act
        $this->googleCloudLogger->emergency($message, $context);

        // Assert
        // Nothing more to assert as behavior is validated via `$this->logger->expects()`
    }

    public function testShouldLogInfoMessageCorrectly(): void
    {
        // Arrange
        $message = 'Test Message';
        $context = ['key' => 'value'];

        $this->logger->expects($this->once())
            ->method('write')
            ->with($this->isInstanceOf(Entry::class));

        // Act
        $this->googleCloudLogger->info($message, $context);

        // Assert
        // Nothing more to assert as behavior is validated via `$this->logger->expects()`
    }

    public function testShouldLogAlertMessageCorrectly(): void
    {
        // Arrange
        $message = 'Test Message';
        $context = ['key' => 'value'];

        $this->logger->expects($this->once())
            ->method('write')
            ->with($this->isInstanceOf(Entry::class));

        // Act
        $this->googleCloudLogger->alert($message, $context);

        // Assert
        // Nothing more to assert as behavior is validated via `$this->logger->expects()`
    }

    public function testShouldLogCriticalMessageCorrectly(): void
    {
        // Arrange
        $message = 'Test Message';
        $context = ['key' => 'value'];

        $this->logger->expects($this->once())
            ->method('write')
            ->with($this->isInstanceOf(Entry::class));

        // Act
        $this->googleCloudLogger->critical($message, $context);

        // Assert
        // Nothing more to assert as behavior is validated via `$this->logger->expects()`
    }

    public function testShouldLogErrorMessageCorrectly(): void
    {
        // Arrange
        $message = 'Test Message';
        $context = ['key' => 'value'];

        $this->logger->expects($this->once())
            ->method('write')
            ->with($this->isInstanceOf(Entry::class));

        // Act
        $this->googleCloudLogger->error($message, $context);

        // Assert
        // Nothing more to assert as behavior is validated via `$this->logger->expects()`
    }

    public function testShouldLogWarningMessageCorrectly(): void
    {
        // Arrange
        $message = 'Test Message';
        $context = ['key' => 'value'];

        $this->logger->expects($this->once())
            ->method('write')
            ->with($this->isInstanceOf(Entry::class));

        // Act
        $this->googleCloudLogger->warning($message, $context);

        // Assert
        // Nothing more to assert as behavior is validated via `$this->logger->expects()`
    }

    public function testShouldLogNoticeMessageCorrectly(): void
    {
        // Arrange
        $message = 'Test Message';
        $context = ['key' => 'value'];

        $this->logger->expects($this->once())
            ->method('write')
            ->with($this->isInstanceOf(Entry::class));

        // Act
        $this->googleCloudLogger->notice($message, $context);

        // Assert
        // Nothing more to assert as behavior is validated via `$this->logger->expects()`
    }

    public function testShouldLogDebugMessageCorrectly(): void
    {
        // Arrange
        $message = 'Test Message';
        $context = ['key' => 'value'];

        $this->logger->expects($this->once())
            ->method('write')
            ->with($this->isInstanceOf(Entry::class));

        // Act
        $this->googleCloudLogger->debug($message, $context);

        // Assert
        // Nothing more to assert as behavior is validated via `$this->logger->expects()`
    }

    public function testShouldPrintfOnException(): void
    {
        // Arrange
        $message = 'Test Message';
        $context = ['key' => 'value'];

        $this->logger->expects($this->once())
            ->method('write')
            ->willThrowException(new Exception('Test Exception'));

        // Act
        ob_start();
        $this->googleCloudLogger->debug($message, $context);
        $output = ob_get_clean();

        // Assert
        $this->assertMatchesRegularExpression('/\("Test Exception" in `.*` at `.*`\)/', $output);
    }

    public function testShouldWriteLogUsingCoroutine(): void
    {
        // Arrange
        $googleCloudLogger = new GoogleCloudLogger(
            driver: $this->logger,
            projectId: 'projectId',
            serviceName: 'serviceName',
            env: 'test',
            useCoroutine: true
        );

        $message = 'Test Message';
        $context = ['key' => 'value'];

        $this->logger->expects($this->once())
            ->method('write')
            ->with($this->isInstanceOf(Entry::class));

        // Act
        $googleCloudLogger->debug($message, $context);

        // Assert
        // Nothing more to assert as behavior is validated via `$this->logger->expects()`
    }
}
