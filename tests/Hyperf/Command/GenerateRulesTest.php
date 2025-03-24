<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Command;

use PHPUnit\Framework\TestCase;
use Serendipity\Example\Game\Domain\Entity\Command\GameCommand;
use Serendipity\Hyperf\Command\GenerateRules;
use Serendipity\Hyperf\Testing\Extension\MakeExtension;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateRulesTest extends TestCase
{
    use MakeExtension;

    public function testConfigure(): void
    {
        $command = $this->make(GenerateRules::class);
        $this->assertSame('dev:rules {entity}', $command->getName());
        $this->assertSame('Export the rules to validate an entity', $command->getDescription());
    }

    public function testHandleNoEntityProvided(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $command = $this->make(GenerateRules::class);
        $input = new ArrayInput([]);

        $command->setInput($input);
        $command->handle();
    }

    public function testHandleEntityDoesNotExist(): void
    {
        // Arrange
        $command = $this->make(GenerateRules::class);
        $messages = [];
        $output = $this->createMock(SymfonyStyle::class);
        $output->method('title')
            ->willReturnSelf();
        $output->method('writeln')
            ->willReturnCallback(function (mixed $string) use (&$messages) {
                $messages[] = $string;
            });
        $command->setOutput($output);

        $input = $this->createMock(InputInterface::class);
        $input->expects($this->once())
            ->method('getArgument')
            ->willReturn('NonExistentClass');
        $command->setInput($input);

        // Act
        $command->handle();

        // Assert
        $this->assertStringContainsString(
            'It was not possible to generate rules for the entity',
            implode('|', $messages),
        );
    }

    public function testHandleGenerateRulesForValidEntity(): void
    {
        // Arrange
        $command = $this->make(GenerateRules::class);
        $messages = [];
        $output = $this->createMock(SymfonyStyle::class);
        $output->method('title')
            ->willReturnSelf();
        $output->method('writeln')
            ->willReturnCallback(function (mixed $string) use (&$messages) {
                $messages[] = $string;
            });
        $command->setOutput($output);

        $input = $this->createMock(InputInterface::class);
        $input->expects($this->once())
            ->method('getArgument')
            ->willReturn(GameCommand::class);
        $command->setInput($input);

        // Act
        $command->handle();

        // Assert
        $this->assertStringContainsString('Rules generated successfully', implode('|', $messages));
    }

    public function testHandleGenerateRulesFromFile(): void
    {
        // Arrange
        $command = $this->make(GenerateRules::class);
        $messages = [];
        $output = $this->createMock(SymfonyStyle::class);
        $output->method('title')
            ->willReturnSelf();
        $output->method('writeln')
            ->willReturnCallback(function (mixed $string) use (&$messages) {
                $messages[] = $string;
            });
        $command->setOutput($output);

        $input = $this->createMock(InputInterface::class);
        $input->expects($this->once())
            ->method('getArgument')
            ->willReturn('src/Example/Game/Domain/Entity/Command/GameCommand.php');
        $command->setInput($input);

        // Act
        $command->handle();

        // Assert
        $this->assertStringContainsString('Rules generated successfully', implode('|', $messages));
    }

    public function testCantHandleNotMappedFile(): void
    {
        // Arrange
        $command = $this->make(GenerateRules::class);
        $messages = [];
        $output = $this->createMock(SymfonyStyle::class);
        $output->method('title')
            ->willReturnSelf();
        $output->method('writeln')
            ->willReturnCallback(function (mixed $string) use (&$messages) {
                $messages[] = $string;
            });
        $command->setOutput($output);

        $input = $this->createMock(InputInterface::class);
        $input->expects($this->once())
            ->method('getArgument')
            ->willReturn('tests/Testing/Stub/Variety.php');
        $command->setInput($input);

        // Act
        $command->handle();

        // Assert
        $this->assertStringContainsString(
            'It was not possible to generate rules for the entity',
            implode('|', $messages)
        );
    }
}
