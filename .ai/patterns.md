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

use App\Example\Domain\Entity\Example;
use App\Example\Domain\Collection\ExampleCollection;

interface ExampleQueryRepository
{
    public function findById(string $id): ?Example;
    public function findAll(): ExampleCollection;
}
```

---

### Input Pattern

Input classes in Serendipity encapsulate and validate incoming data for use cases such as commands, queries, or API endpoints. They extend the base `Input` class and define a `rules()` method that specifies validation rules for each field, following a convention similar to Laravel's validation system. This approach ensures consistent, reusable, and testable input validation across the application.

**Characteristics:**

- Extend the base `Input` class
- Define a `rules()` method returning an array of validation rules
- Support for required, optional, and nested fields
- Used for both write (command) and read (query/search) actions

**Examples:**

*ExampleCreateInput (for write actions):*
```php
<?php

declare(strict_types=1);

namespace App\Example\Presentation\Input;

use Serendipity\Presentation\Input;

class ExampleCreateInput extends Input
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'slug' => ['required', 'string'],
            'published_at' => ['required', 'date'],
            'data' => ['required', 'array'],
            'features' => ['required', 'array'],
            'features.*.name' => ['required', 'string'],
            'features.*.description' => ['required', 'string'],
            'features.*.enabled' => ['required', 'boolean'],
        ];
    }
}
```

*ExampleReadInput (for query/read actions):*
```php
<?php

declare(strict_types=1);

namespace App\Example\Presentation\Input;

use Serendipity\Presentation\Input;

class ExampleReadInput extends Input
{
    public function rules(): array
    {
        return [
            'id' => 'required|string',
        ];
    }
}
```

*ExampleSearchInput (for search/filter actions):*
```php
<?php

declare(strict_types=1);

namespace App\Example\Presentation\Input;

use Serendipity\Presentation\Input;

class ExampleSearchInput extends Input
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string'],
            'slug' => ['sometimes', 'string'],
        ];
    }
}
```

These input classes are typically used in controllers or handlers to validate incoming request data before passing it to domain services or repositories.

---

### Example Usage: Query and Write Actions

#### Query (Read) Action Example

```php
// Querying an entity by ID using the query repository
$example = $exampleQueryRepository->findById($id);
if ($example !== null) {
    // Use the entity for read-only operations
    echo $example->id;
    echo $example->name;
}
```

#### Write (Command) Action Example

```php
// Creating a new entity using input and command repository
$input = new ExampleInput(
    name: 'Sample',
    slug: 'sample',
    publishedAt: new Timestamp(),
    data: ['foo' => 'bar'],
);
$command = new ExampleCommand(
    name: $input->name,
    slug: $input->slug,
    publishedAt: $input->publishedAt,
    data: $input->data,
    features: new FeatureCollection([]),
);
$newId = $exampleCommandRepository->create($command);
```

---

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
