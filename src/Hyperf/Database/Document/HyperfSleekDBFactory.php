<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Database\Document;

use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Serendipity\Infrastructure\Database\Document\SleekDBFactory;

use function Serendipity\Type\Cast\toArray;

readonly class HyperfSleekDBFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function make(ContainerInterface $container): SleekDBFactory
    {
        $config = $container->get(ConfigInterface::class);
        $options = toArray($config->get('databases.sleek'));
        return new SleekDBFactory($options);
    }
}
