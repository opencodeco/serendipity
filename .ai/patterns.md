# Serendipity Design Patterns

This document describes the structure of elements found in the base code, specifically analyzing the components
implemented in the `src/Example` directory.

## Structure of Elements

This guide describes the format of the components that can be used to implement the `Serendipity` pattern.

#### Entity Pattern (Actual Implementation)

The project uses a different Entity pattern than traditional DDD. Entities use attributes for metadata and extend
command classes.

**Characteristics**:

- Uses `#[Managed]` attributes for field management
- Uses `#[Pattern]` attributes for validation
- Extends command classes for separation of concerns
- Integrates with base Entity class from the package

The entities are immutable and use constructor injection for dependencies. They are designed to encapsulate the business
logic and be used in query operations.

**Example location src/Domain/Entity/Example.php**:

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

Entities extend command classes that inherit from a base Entity class. The command entities are used to encapsulate the
write operations.

**Example location src/Domain/Entity/Command/ExampleCommand.php**:

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

Type-safe collections that extend a base Collection class with validation.

**Example location src/Domain/Collection/ExampleCollection.php**:

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

Separate repositories for command and query operations.

**Command Repository from src/Domain/Repository/ExampleCommandRepository.php**:

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

**Query Repository from src/Domain/Repository/ExampleQueryRepository.php**:

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

### Health Components

The `src/Example/Health` directory demonstrates action and input patterns:

#### Action Pattern

Readonly classes with dependency injection using the `__invoke` method.

**Example location srcealth/HealthAction.php**:

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

Input classes that extend a base Input class with validation rules.

**Example location `src/Presentation/Input/HealthInput.php`**:

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

The `src/` directory demonstrates the following patterns actually used in the project:

1. **Entity Pattern**: Using attributes for metadata and extending command classes
2. **Command Pattern**: Separating entity concerns through command inheritance
3. **Collection Pattern**: Type-safe collections with validation
4. **CQRS Repository Pattern**: Separate command and query repositories
5. **Action Pattern**: Readonly classes with `__invoke` method and dependency injection
6. **Input Pattern**: Validation-enabled input classes extending base Input class

These patterns differ significantly from traditional DDD patterns and represent the actual architectural approach used
in this Serendipity project.
