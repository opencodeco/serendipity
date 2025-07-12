# Serendipity Design Patterns

This document outlines the structure of elements intended for use in projects built with the Serendipity library. It
focuses on the components provided in the `src/Example` directory, serving as practical references for implementation.

## Structure of Elements

This guide explains the expected structure of components that follow the `Serendipity` pattern and how they should be
applied in consuming projects.

#### Entity Pattern (Expected Usage)

Serendipity defines a distinct Entity pattern that deviates from traditional DDD. Entities are constructed with metadata
attributes and inherit from command-specific base classes.

**Characteristics**:

* Uses `#[Managed]` attributes for field tracking
* Uses `#[Pattern]` attributes for input validation
* Extends command classes to separate write responsibilities
* Inherits from the base `Entity` class provided by the library

Entities are immutable, receive their dependencies through constructor injection, and are intended to encapsulate domain
logic used in query operations.

**Example from `src/Domain/Entity/Example.php`**:

```php
<?php

declare(strict_types=1);

namespace App\Example\Domain\Entity;

use Serendipity\Domain\Support\Reflective\Attribute\Managed;
use Serendipity\Domain\Support\Reflective\Attribute\Pattern;
use Serendipity\Domain\Type\Timestamp;
use App\Example\Domain\Collection\Example\FeatureCollection;
use App\Example\Domain\Entity\Command\ExampleCommand;

class Example extends ExampleCommand
{
    public function __construct(
        #[Managed('id')]
        public readonly string $id,
        #[Managed('timestamp')]
        public readonly Timestamp $createdAt,
        #[Managed('timestamp')]
        public readonly Timestamp $updatedAt,
        #[Pattern('/^[a-zA-Z]{1,255}$/')]
        string $name,
        string $slug,
        Timestamp $publishedAt,
        array $data,
        FeatureCollection $features,
    ) {
        parent::__construct(
            name: $name,
            slug: $slug,
            publishedAt: $publishedAt,
            data: $data,
            features: $features,
        );
    }
}
```

#### Command Pattern

Entities extend command classes that inherit from a shared base. These command classes encapsulate write behavior and
represent mutation-focused versions of the domain model.

**Example from `src/Domain/Entity/Command/ExampleCommand.php`**:

```php
<?php

declare(strict_types=1);

namespace App\Example\Domain\Entity\Command;

use Serendipity\Domain\Entity\Entity;
use Serendipity\Domain\Type\Timestamp;
use App\Example\Domain\Collection\Example\FeatureCollection;

class ExampleCommand extends Entity
{
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly Timestamp $publishedAt,
        public readonly array $data,
        public readonly FeatureCollection $features,
    ) {
    }
}
```

#### Collection Pattern

Projects should implement type-safe collections by extending Serendipity’s base `Collection` class and applying item
validation.

**Example from `src/Domain/Collection/ExampleCollection.php`**:

```php
<?php

declare(strict_types=1);

namespace App\Example\Domain\Collection;

use Serendipity\Domain\Collection\Collection;
use App\Example\Domain\Entity\Example;

/**
 * @extends Collection<Example>
 */
final class ExampleCollection extends Collection
{
    public function current(): Example
    {
        return $this->validate($this->datum());
    }

    protected function validate(mixed $datum): Example
    {
        return ($datum instanceof Example) ? $datum : throw $this->exception(Example::class, $datum);
    }
}
```

#### CQRS Repository Pattern

Projects should implement separate repositories for command (write) and query (read) concerns, following a CQRS
architecture.

**Command repository in `src/Domain/Repository/ExampleCommandRepository.php`**:

```php
<?php

declare(strict_types=1);

namespace App\Example\Domain\Repository;

use Serendipity\Domain\Exception\ManagedException;
use App\Example\Domain\Entity\Command\ExampleCommand;

interface ExampleCommandRepository
{
    /**
     * @throws ManagedException
     */
    public function create(ExampleCommand $game): string;

    public function delete(string $id): bool;
}
```

**Query repository in `src/Domain/Repository/ExampleQueryRepository.php`**:

```php
<?php

declare(strict_types=1);

namespace App\Example\Domain\Repository;

use App\Example\Domain\Collection\ExampleCollection;
use App\Example\Domain\Entity\Example;

interface ExampleQueryRepository
{
    public function getExample(string $id): ?Example;

    public function getExamples(array $filters = []): ExampleCollection;
}
```

#### Action Pattern

Actions are readonly classes that rely on constructor injection and expose a single `__invoke` method. They are commonly
used in service layers or application logic.

**Example from `src/Presentation/Action/HealthAction.php`**:

```php
<?php

declare(strict_types=1);

namespace App\Health;

use Psr\Log\LoggerInterface;

readonly class HealthAction
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function __invoke(HealthInput $input): string
    {
        $value = $input->value('message', 'Kicking ass and taking names!');
        $this->logger->emergency(sprintf('Health action message using emergency: %s', $value));
        $this->logger->alert(sprintf('Health action message using alert: %s', $value));
        $this->logger->critical(sprintf('Health action message using critical: %s', $value));
        $this->logger->error(sprintf('Health action message using error: %s', $value));
        $this->logger->warning(sprintf('Health action message using warning: %s', $value));
        $this->logger->notice(sprintf('Health action message using notice: %s', $value));
        $this->logger->info(sprintf('Health action message using info: %s', $value));
        $this->logger->debug(sprintf('Health action message using debug: %s', $value));
        return $value;
    }
}
```

#### Input Pattern

Input classes extend Serendipity’s base `Input` class and define validation rules for structured data. They are designed
to be used as typed argument objects for actions.

**Example from `src/Presentation/Input/HealthInput.php`**:

```php
<?php

declare(strict_types=1);

namespace App\Health;

use Serendipity\Presentation\Input;

final class HealthInput extends Input
{
    public function rules(): array
    {
        return [
            'message' => 'sometimes|string',
        ];
    }
}
```

## Summary of Implemented Patterns

The components within the `src/` directory exemplify the following architectural patterns provided by Serendipity:

1. **Entity Pattern** – Metadata via attributes and inheritance from command classes
2. **Command Pattern** – Write-focused structure through command inheritance
3. **Collection Pattern** – Type-safe, validated collection wrappers
4. **CQRS Repository Pattern** – Separation of command and query interfaces
5. **Action Pattern** – Readonly classes with constructor-based dependency injection and `__invoke` usage
6. **Input Pattern** – Validation-enabled request objects extending a base Input class

These patterns form the core architectural approach promoted by the Serendipity library and are intended to be followed
consistently across consuming projects.
