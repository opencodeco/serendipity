# Serendipity Design Patterns

This document outlines the design patterns, architectural patterns, and coding patterns used throughout the Serendipity project.

## Architectural Patterns

### Dependency Injection Pattern

The project heavily uses dependency injection for loose coupling and testability.

**Implementation**:
- Hyperf's built-in DI container
- Constructor injection preferred
- Interface-based dependencies
- Service providers for complex configurations

**Example**:
```php
<?php

declare(strict_types=1);

namespace Serendipity\Domain\Service;

use Serendipity\Domain\Repository\UserRepositoryInterface;

final class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function findUser(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }
}
```

### Repository Pattern

Used for data access abstraction and testability.

**Structure**:
- Interface defines contract
- Implementation handles data persistence
- Domain entities remain persistence-agnostic

**Example**:
```php
<?php

declare(strict_types=1);

namespace Serendipity\Domain\Repository;

use Serendipity\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
    public function save(User $user): void;
    public function delete(User $user): void;
}
```

### Aspect-Oriented Programming (AOP)

Used for cross-cutting concerns like logging, caching, and validation.

**Common Aspects**:
- Logging aspects for method entry/exit
- Caching aspects for expensive operations
- Validation aspects for input sanitization
- Transaction aspects for database operations

**Example**:
```php
<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Aspect;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;

#[Aspect]
class LoggingAspect extends AbstractAspect
{
    public array $classes = [
        'Serendipity\Domain\Service\*',
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $className = $proceedingJoinPoint->className;
        $method = $proceedingJoinPoint->methodName;
        
        // Log method entry
        logger()->info("Entering {$className}::{$method}");
        
        $result = $proceedingJoinPoint->process();
        
        // Log method exit
        logger()->info("Exiting {$className}::{$method}");
        
        return $result;
    }
}
```

## Domain-Driven Design Patterns

### Entity Pattern

Domain entities with identity and business logic.

**Characteristics**:
- Unique identity
- Encapsulated business rules
- Immutable when possible
- Rich domain model

**Example**:
```php
<?php

declare(strict_types=1);

namespace Serendipity\Domain\Entity;

final class User
{
    private function __construct(
        private readonly int $id,
        private string $email,
        private string $name
    ) {}

    public static function create(int $id, string $email, string $name): self
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address');
        }

        return new self($id, $email, $name);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function changeName(string $name): void
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Name cannot be empty');
        }
        
        $this->name = $name;
    }
}
```

### Value Object Pattern

Immutable objects representing values without identity.

**Example**:
```php
<?php

declare(strict_types=1);

namespace Serendipity\Domain\ValueObject;

final class Email
{
    private function __construct(
        private readonly string $value
    ) {}

    public static function fromString(string $email): self
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }

        return new self($email);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(Email $other): bool
    {
        return $this->value === $other->value;
    }
}
```

### Domain Service Pattern

Encapsulates domain logic that doesn't naturally fit in entities or value objects.

**Example**:
```php
<?php

declare(strict_types=1);

namespace Serendipity\Domain\Service;

final class UserRegistrationService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly EmailService $emailService
    ) {}

    public function registerUser(string $email, string $name): User
    {
        if ($this->userRepository->existsByEmail($email)) {
            throw new UserAlreadyExistsException();
        }

        $user = User::create(
            $this->userRepository->nextId(),
            $email,
            $name
        );

        $this->userRepository->save($user);
        $this->emailService->sendWelcomeEmail($user);

        return $user;
    }
}
```

## Coroutine Patterns

### Coroutine Pool Pattern

Managing coroutine execution with pools for resource control.

**Usage**:
- Database connection pools
- HTTP client pools
- Worker coroutine pools

### Async/Await Pattern

Handling asynchronous operations in coroutine context.

**Example**:
```php
<?php

declare(strict_types=1);

use Hyperf\Coroutine\Coroutine;

// Concurrent execution
$results = [];
$results[] = Coroutine::create(function () {
    return $this->fetchUserData($userId);
});
$results[] = Coroutine::create(function () {
    return $this->fetchUserPreferences($userId);
});

// Wait for all coroutines to complete
$userData = $results[0]->get();
$preferences = $results[1]->get();
```

## Testing Patterns

### Arrange-Act-Assert (AAA) Pattern

Standard testing structure for all test methods.

**Structure**:
1. **Arrange**: Set up test data and dependencies
2. **Act**: Execute the code under test
3. **Assert**: Verify the expected outcome

### Test Double Patterns

**Mock Objects**: For behavior verification
**Stub Objects**: For state-based testing
**Fake Objects**: For simplified implementations

**Example**:
```php
<?php

declare(strict_types=1);

namespace Serendipity\Test\Domain\Service;

use PHPUnit\Framework\TestCase;
use Serendipity\Domain\Repository\UserRepositoryInterface;
use Serendipity\Domain\Service\UserService;

final class UserServiceTest extends TestCase
{
    public function testFindUserReturnsUserWhenExists(): void
    {
        // Arrange
        $userId = 1;
        $expectedUser = User::create($userId, 'test@example.com', 'Test User');
        
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willReturn($expectedUser);
            
        $userService = new UserService($userRepository);

        // Act
        $result = $userService->findUser($userId);

        // Assert
        $this->assertSame($expectedUser, $result);
    }
}
```

## Error Handling Patterns

### Exception Hierarchy Pattern

Structured exception hierarchy for different error types.

**Structure**:
```
SerendipityException (base)
├── DomainException
│   ├── UserNotFoundException
│   └── InvalidUserDataException
├── InfrastructureException
│   ├── DatabaseConnectionException
│   └── ExternalServiceException
└── ApplicationException
    ├── ValidationException
    └── AuthorizationException
```

### Result Pattern

Alternative to exceptions for expected error conditions.

**Example**:
```php
<?php

declare(strict_types=1);

namespace Serendipity\Domain\Support;

final class Result
{
    private function __construct(
        private readonly mixed $value,
        private readonly ?string $error
    ) {}

    public static function success(mixed $value): self
    {
        return new self($value, null);
    }

    public static function failure(string $error): self
    {
        return new self(null, $error);
    }

    public function isSuccess(): bool
    {
        return $this->error === null;
    }

    public function getValue(): mixed
    {
        if (!$this->isSuccess()) {
            throw new RuntimeException('Cannot get value from failed result');
        }
        
        return $this->value;
    }

    public function getError(): string
    {
        return $this->error ?? '';
    }
}
```

## Data Access Patterns

### Unit of Work Pattern

Managing database transactions and change tracking.

### Data Mapper Pattern

Separating domain objects from database representation.

### Query Object Pattern

Encapsulating complex queries in objects.

**Example**:
```php
<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Query;

final class FindActiveUsersQuery
{
    public function __construct(
        private readonly ?int $limit = null,
        private readonly ?int $offset = null
    ) {}

    public function toSql(): string
    {
        $sql = 'SELECT * FROM users WHERE active = true';
        
        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }
        
        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }
        
        return $sql;
    }
}
```

## Performance Patterns

### Lazy Loading Pattern

Deferring expensive operations until needed.

### Caching Patterns

- **Cache-Aside**: Application manages cache
- **Write-Through**: Write to cache and database simultaneously
- **Write-Behind**: Write to cache immediately, database later

### Connection Pooling Pattern

Reusing database connections for better performance.

## Configuration Patterns

### Environment-Based Configuration

Different configurations for different environments.

### Feature Flag Pattern

Controlling feature availability through configuration.

**Example**:
```php
<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Feature;

final class FeatureFlag
{
    public function __construct(
        private readonly array $flags
    ) {}

    public function isEnabled(string $feature): bool
    {
        return $this->flags[$feature] ?? false;
    }
}
```
