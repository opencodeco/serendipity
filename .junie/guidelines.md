# Serendipity Development Guidelines

This document provides essential information for developers working on the Serendipity project. It includes
build/configuration instructions, testing information, and additional development guidelines.

## Build/Configuration Instructions

### Prerequisites

- PHP 8.3 or higher
- Docker and Docker Compose
- Composer

### Environment Setup

1. Clone the repository
2. Create a `.env` file based on `.env.example`:
   ```bash
   cp .env.example .env
   ```
3. Configure the environment variables in the `.env` file according to your needs

### Project Setup

The project includes a comprehensive makefile that simplifies common tasks:

```bash
# Complete setup (prune, install dependencies, start containers, run migrations)
make setup

# Start the project
make up

# Stop the project
make down

# Access the application container
make bash

# Install dependencies
make install

# Run database migrations
make migrate
```

### Docker Environment

The project uses Docker Compose with the following services:

- **app**: Hyperf PHP 8.3 application
- **postgres**: PostgreSQL 16.2 database
- **mongo**: MongoDB 6.0 with replica set configuration

## Testing Information

### Running Tests

Tests are executed using PHPUnit with a custom wrapper that supports Hyperf's coroutine-based architecture. All tests
should be run within the Docker container using the make commands:

```bash
# Run all tests with coverage report
make test

# Run specific tests (executed in the Docker container)
make bash
# Then inside the container:
composer test -- --filter=SimpleTest

# Run tests with specific options
make bash
# Then inside the container:
composer test -- --group=unit
```

### Adding New Tests

1. Create a new test class in the `tests` directory, following the existing namespace structure
2. Extend `PHPUnit\Framework\TestCase` for unit tests
3. Use the `@internal` annotation for test classes
4. Follow the Arrange-Act-Assert pattern in test methods
5. Use descriptive test method names prefixed with `test`

### Example Test

Here's a simple example test:

```php
<?php

declare(strict_types=1);

namespace Serendipity\Test\Example;

use PHPUnit\Framework\TestCase;

final class SimpleTest extends TestCase
{
    public function testSimpleAssertion(): void
    {
        // Arrange
        $expected = true;

        // Act
        $actual = true;

        // Assert
        $this->assertEquals($expected, $actual);
    }
}
```

## Code Quality and Style

The project uses several tools to maintain code quality:

### Code Style

PHP-CS-Fixer is configured with a combination of PSR2, Symfony, DoctrineAnnotation, and PhpCsFixer rule sets. Key style
rules include:

- Short array syntax (`[]` instead of `array()`)
- Single space around concatenation operators
- Single quotes for strings
- Strict type declarations
- Ordered imports (classes, functions, constants)

To check and fix code style:

```bash
# Check code style
make lint-phpcs

# Fix code style issues
make fix
```

### Static Analysis

The project uses multiple static analysis tools:

```bash
# Run PHPStan
make lint-phpstan

# Run Psalm
make lint-psalm

# Run Deptrac (dependency analysis)
make lint-deptrac

# Run PHP Mess Detector
make lint-phpmd

# Run Rector (with dry-run)
make lint-rector

# Run all linting tools
make lint
```

### Continuous Integration

The project includes a CI command that runs all linting tools and tests:

```bash
make ci
```

## Project Structure

- `bin/`: Command-line scripts
- `config/`: Application configuration
- `migrations/`: Database migrations
- `src/`: Source code
- `tests/`: Test files
- `vendor/`: Dependencies (managed by Composer)

## Additional Development Information

### Hyperf Framework

This project is built on the Hyperf framework, a high-performance, coroutine-based PHP framework. Key features include:

- Dependency injection container
- Aspect-oriented programming
- Coroutine-based concurrency
- High-performance HTTP server

### Database Access

The project supports both PostgreSQL and MongoDB:

- PostgreSQL is used for relational data
- MongoDB is configured with a replica set for distributed data storage

### Logging

The application uses Monolog for logging, with configuration for different log levels. In development, logs are output
to stdout.

### Error Handling

The application includes custom exception handlers and integration with Sentry for error tracking.

### Makefile Commands

The makefile includes various helpful commands for development:

```bash
# Show all available commands
make help
```
