<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Database\Document;

use SleekDB\Exceptions\InvalidArgumentException;
use SleekDB\Exceptions\InvalidConfigurationException;
use SleekDB\Exceptions\IOException;
use SleekDB\Store;

use function Serendipity\Type\Cast\toArray;
use function Serendipity\Type\Cast\toString;

class SleekDBFactory
{
    public function __construct(private readonly array $options)
    {
    }

    /**
     * @throws InvalidConfigurationException
     * @throws InvalidArgumentException
     * @throws IOException
     */
    public function make(string $resource): Store
    {
        $path = toString($this->options['path'] ?? '');
        $configuration = toArray($this->options['configuration'] ?? []);
        return new Store($resource, $path, $configuration);
    }
}
